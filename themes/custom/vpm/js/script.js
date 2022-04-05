(function($, Drupal) {
    'use strict';

    Drupal.behaviors.vpm = {
        attach: function(context, settings) {



            function genericSocialShare(url) {
                window.open(url, 'sharer', 'toolbar=0,status=0,width=648,height=395');
                return true;
            }


            jQuery(".navbar-toggler").unbind().click(function() {
                var display = jQuery('.navbar-collapse').css('display');
                console.log("botont");
                if (display === "block") {
                    jQuery('#navDesktop').css("display", "none");
                } else {
                    jQuery('#navDesktop').css("display", "block");
                }

            });


            function shareUrls(name) {
                var permalink = encodeURI(window.location.href)

                var urlTwitter = 'https://twitter.com/intent/tweet?text=Conoce el contenido "' + name + '" en Portal Biblioredes ' + permalink
                jQuery('.share-twitter').attr('href', 'javascript:void(0)')
                jQuery('.share-twitter').click(function() {
                    genericSocialShare(urlTwitter)
                })

                var urlFacebook = "https://www.facebook.com/sharer/sharer.php?u=" + permalink
                jQuery('.share-facebook').attr('href', 'javascript:void(0)')
                jQuery('.share-facebook').click(function() {
                    genericSocialShare(urlFacebook)
                })
            }

            shareUrls($("#tituloContenido").val());


            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop()) {
                    jQuery('#ir-arriba').fadeIn();
                } else {
                    jQuery('#ir-arriba').fadeOut();
                }
            });
            // jQuery('main', context).once('mei_library_turn').each(function() {
            //     jQuery(".demo").jspanzoom({ scrollZoom: true, constrainType: 'width', constrainSize: '200px' });
            // });
            jQuery("#ir-arriba").click(function() {
                jQuery("html, body").animate({ scrollTop: 0 }, 0);
            });
            $(document).ready(function($) {


                var imgGrande = $("#urlImg").val();
                console.log(imgGrande)
                var iv1 = $("#viewer").iviewer({
                    src: "http://quinsac.patrimoniocultural.gob.cl/sites/default/original_image_files/LFD1655/LFD1655.01.JPG",
                    update_on_resize: false,
                    zoom_animation: false,
                    mousewheel: false,
                    onMouseMove: function(ev, coords) {},
                    onStartDrag: function(ev, coords) { return true; }, //this image will not be dragged
                    onDrag: function(ev, coords) {}
                });

                $("#in").click(function() { iv1.iviewer('zoom_by', 1); });
                $("#out").click(function() { iv1.iviewer('zoom_by', -1); });
                $("#fit").click(function() { iv1.iviewer('fit'); });
                $("#orig").click(function() { iv1.iviewer('set_zoom', 100); });
                $("#update").click(function() { iv1.iviewer('update_container_info'); });


                $("#chimg").click(function() {
                    iv2.iviewer('loadImage', imgGrande);
                    return false;
                });

                var fill = false;
                $("#fill").click(function() {
                    fill = !fill;
                    iv2.iviewer('fill_container', fill);
                    return false;
                });






            });




        }
    };

})(jQuery, Drupal);