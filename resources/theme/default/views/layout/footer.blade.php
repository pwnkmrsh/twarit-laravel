<div class="clear"></div>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-1">
                @include('_particles.header.language_picker')
                @include('_particles.footer.logo')
            </div>
            <div class="col-3">
               {{ menu('footer-menu', array(
                    'ul_class' => 'foot-menu',
                    'a_class' => 'footer-menu__item'
                )) }}
              <div class="clear"></div>
                 @include('_particles.footer.copyright')
            </div>
        </div>
        <div class="clear"></div>
    </div>
</footer>
