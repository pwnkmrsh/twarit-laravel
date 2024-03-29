<?php

namespace App\Managers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadManager
{
    /**
     * Akbilisim API URI.
     *
     * @todo change the api url
     *
     * @var stringvom
     */
    public $path = 'upload/tmp/';

    public $mimes = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    public $mime;

    public $max = 2000;

    /**
     * Retrieve a file from the request.
     *
     * @var string
     */
    public $file_name = '';

    /**
     * Retrieve a file from the request.
     *
     * @var string
     */
    public $file_name_size = '';

    /**
     * Retrieve a file from the request.
     *
     * @var string
     */
    public $file_mime = '';

    /**
     * Saves encoded file in filesystem
     *
     * @var mixed
     */
    public $file;

    /**
     * Uploaded file
     *
     * @var mixed
     */
    public $uploaded_file;

    /**
     * Saved Files
     *
     * @var array
     */
    public $uploads = [];

    public $request;

    public $full_path;

    public $date_path;

    public $gif = false;

    public $is_s3 = false;

    public function __construct()
    {
        $this->is_s3 = $this->checkS3Conf();
        $this->max = get_buzzy_config('user_max_fileupload_size', $this->max);
        $this->date_path = date('Y-m') . '/' . date('d') . '/';
    }

    public function path($path)
    {
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/') . '/';
        $this->path = $path;
    }

    public function date_path($date_path)
    {
        $date_path = ltrim($date_path, '/');
        $date_path = rtrim($date_path, '/') . '/';
        $this->date_path = $date_path;
    }

    public function name($file_name)
    {
        $file_name = ltrim($file_name, '/');
        $file_name = rtrim($file_name, '/');
        $this->file_name = $file_name;
    }

    public function mimes($mimes)
    {
        $this->mimes = $mimes;
    }

    public function max($max)
    {
        $this->max = (int) $max;
    }

    public function mime($mime)
    {
        $this->mime = trim($mime);
    }

    public function file_mime($mime)
    {
        $this->file_mime = str_replace(array('image/', 'video/'), '', str_replace('.', '', trim($mime)));
    }

    public function acceptGif()
    {
        $this->gif = true;
    }

    public function file(Request $request, $file_input = 'file')
    {
        try {
            if (!$request->hasFile($file_input)) {
                throw new \Exception("No files");
            }

            $validator = $this->validator($request->all(), $file_input);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $this->file = $request->file($file_input);
            $this->uploaded_file = $this->file;

            $this->file_mime($this->file->getMimeType());

            $this->file_name = Str::slug(str_replace($this->mimes, '', $this->file->getClientOriginalName())) . '-' . $this->file_name;

            if (!$this->checkMime() || !$this->checkUrlMime(strtolower($this->file->getClientOriginalName()))) {
                throw new \Exception("No valid files");
            }
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    public function setUrlFile($file)
    {
        if (!empty($file) && empty($this->file)) {
            // set url file
            $this->file = substr($file, 0, 4) === 'http' ? $file : $this->public_path($file);
            $this->uploaded_file = $this->file;
        }
    }

    public function make()
    {
        try {
            if (empty($this->file)) {
                throw new \Exception("No valid image to make");
            }

            $file = Image::make($this->file);
            $mime = $file->mime();

            $this->file_mime($mime);

            if (!$this->checkMime()) {
                throw new \Exception("No valid images");
            }

            if ($this->gif && $mime === 'gif') {
                return;
            }

            $this->file = $file;

            return $this->file;
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }

        return false;
    }

    public function save($args = [])
    {
        try {
            if ($this->file_mime === 'mp4') {
                $this->mime = 'mp4';
                $this->saveVideo($args);
                return;
            }

            if ($this->gif && $this->file_mime === 'gif') {
                $this->mime = 'gif';
                $this->saveGif();
                return;
            }

            $this->saveImage($args);
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }


    public function saveImage($args)
    {
        try {
            if (isset($args['image_size'])) {
                $this->file_name_size = $args['image_size'];
            }

            $fit_width = isset($args['fit_width']) ? $args['fit_width'] : null;
            $fit_height = isset($args['fit_height']) ? $args['fit_height'] : null;
            $fit_call = isset($args['fit_call']) ? $args['fit_call'] : null;
            $fit_pos = isset($args['fit_pos']) ? $args['fit_pos'] : 'center';

            if ($fit_width !== null  || $fit_height !== null) {
                $this->file->fit($args['fit_width'], $fit_height, $fit_call, $fit_pos);
            }

            $resize_width = isset($args['resize_width']) ? $args['resize_width'] : null;
            $resize_height = isset($args['resize_height']) ? $args['resize_height'] : null;
            $resize_call = isset($args['resize_call']) ? $args['resize_call'] : function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            };

            if ($resize_width !== null || $resize_height !== null) {
                $this->file->resize($resize_width, $resize_height, $resize_call);
            }

            $full_path = $this->getSaveFullPath();

            if ($this->is_s3) {
                Storage::disk('s3')->put($full_path, $this->file->stream()->__toString());
            } else {
                $this->createFolder();
                $this->file->save($this->public_path($full_path));
                $this->checFileMime($this->public_path($full_path));
            }

            $this->uploads[] = array_merge($args, [
                'path' => $full_path
            ]);
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    public function saveVideo($args)
    {
        try {
            $full_path = $this->getSaveFullPath();

            if ($this->is_s3) {
                Storage::disk('s3')->put($full_path, fopen($this->file, 'r+'));
            } else {
                $this->createFolder();
                $this->file->move($this->public_path($this->getSavePath()), $this->getSaveName());
                $this->checFileMime($this->public_path($full_path));
            }

            $this->uploads[] = array_merge($args, [
                'path' => $full_path
            ]);
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    public function saveGif()
    {
        try {
            $full_path = $this->getSaveFullPath();

            if ($this->is_s3) {
                Storage::disk('s3')->put($full_path, file_get_contents($this->file));
            } else {
                if ($this->uploaded_file && method_exists($this->uploaded_file, 'move')) {
                    $this->createFolder();
                    $this->uploaded_file->move($this->public_path($this->getSavePath()), $this->getSaveName());
                } elseif (is_string($this->uploaded_file)) { // url file
                    $this->createFolder();
                    copy($this->uploaded_file, $this->public_path($full_path));
                    $this->checFileMime($this->public_path($full_path));
                }
            }

            $this->uploads[] = [
                'path' => $full_path
            ];
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    public function move()
    {
        try {
            $full_path = $this->getSaveFullPath();

            if ($this->is_s3) {
                Storage::disk('s3')->put($full_path, file_get_contents($this->file));
            } else {
                $this->createFolder();
                if ($this->file && method_exists($this->file, 'move')) {
                    $this->file->move($this->public_path($this->getSavePath()), $this->getSaveName());
                } elseif (is_string($this->file)) { // url file
                    rename($this->file, $this->public_path($full_path));
                }
            }

            $this->checFileMime($this->public_path($full_path));

            $this->uploads[] = [
                'path' => $full_path
            ];
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    public function currentDelete($file)
    {
        $this->delete($file);
    }

    public function delete($file)
    {
        try {
            if ($this->is_s3) {
                // for old versions
                if (strpos($file, 'amazonaws.com/upload') > 0) {
                    $file_r = explode('/upload', $file);
                    $file = 'upload' . $file_r[1];
                }
                $file = ltrim($file);

                if (Storage::disk('s3')->exists($file)) {
                    Storage::disk('s3')->delete($file);
                }
            } else {
                $this->removeFile($this->public_path($file));
            }
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }

    /**
     * Validator of question posts
     *
     * @param $inputs
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $inputs, $file_input)
    {
        $mimes = implode(',', $this->mimes);

        $rules = [
            $file_input => 'required|mimes:' . $mimes . '|max:' . $this->max,
        ];

        return Validator::make($inputs, $rules);
    }

    public function getSavedUploads()
    {
        return $this->uploads;
    }

    public function getSaveMime()
    {
        return $this->mime ? $this->mime : $this->file_mime;
    }

    public function getSaveName()
    {
        if ($this->file_name_size) {
            return $this->file_name . '-' . $this->file_name_size . '.' . $this->getSaveMime();
        }

        return $this->file_name . '.' . $this->getSaveMime();
    }

    public function getSavePath()
    {
        if ($this->date_path) {
            return $this->path . $this->date_path;
        }

        return $this->path;
    }

    public function getSaveFullPath()
    {
        $this->full_path = $this->getSavePath() . $this->getSaveName();

        return $this->full_path;
    }

    public function getPathforSave()
    {
        if ($this->date_path) {
            return $this->date_path .  $this->file_name;
        }

        return $this->file_name;
    }

    public function getPathforSaveWithMime()
    {
        return  $this->getPathforSave() . '.' . $this->getSaveMime();
    }

    public function getFullPath()
    {
        return $this->full_path;
    }

    public function getFullUrl()
    {
        if ($this->is_s3) {
            return Storage::disk('s3')->url(ltrim($this->full_path, '/'));
        }

        return $this->full_path;
    }

    public function checkMime($mime = '')
    {
        if (empty($mime)) {
            $mime = $this->file_mime;
        }

        return in_array($mime, $this->mimes);
    }

    public function checkUrlMime($url)
    {
        foreach ($this->mimes as $mime) {
            if (strpos($url, '.' . $mime) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extra Step
     *
     * @param string $public_path
     * @return void
     */
    public function checFileMime($public_path)
    {
        if (!$this->checkUrlMime($public_path)) {
            $this->removeFile($public_path);
            throw new \Exception("No valid files");
        }
    }

    public function createFolder()
    {
        File::makeDirectory($this->public_path($this->getSavePath()), 0755, true, true);
    }

    public function removeFile($public_path)
    {
        if (!File::exists($public_path)) {
            return;
        }

        File::delete($public_path);

        if (file_exists($public_path)) {
            @unlink($public_path);
        }
    }

    public function public_path($path)
    {
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');
        $path = str_replace(url('/'), '', $path);
        return public_path($path);
    }

    public function checkS3Conf()
    {
        if (env('FILESYSTEM_DRIVER') !== "s3") {
            return false;
        }

        if (
            empty(env("AWS_DEFAULT_REGION"))
            || empty(env("AWS_BUCKET"))
            || empty(env("AWS_ACCESS_KEY_ID"))
            || empty(env("AWS_SECRET_ACCESS_KEY"))
        ) {
            return false;
        }

        return true;
    }
}
