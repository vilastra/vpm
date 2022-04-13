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
            //     jQuery(".viewer").jspanzoom({ scrollZoom: true, constrainType: true, constrainSize: 720 });
            // });
            jQuery("#ir-arriba").click(function() {
                jQuery("html, body").animate({ scrollTop: 0 }, 0);
            });
            $(document).ready(function($) {


                // var timeline;
                // timeline = new TL.Timeline('timeline-embed', jsonFile);
                var timeline;
                var options = {
                    initial_zoom: 2,
                    timenav_position: 'top',
                    timenav_height_min: 360,
                    language: 'es',
                    height: 700,
                    duration: 500,

                }
                var timeline = new TL.Timeline('timeline-embed',
                    jsonFile,
                    options);




                var picture = $('#sample_picture');
                // Make sure the image is completely loaded before calling the plugin
                picture.one('load', function() {
                    // Initialize plugin (with custom event)
                    picture.guillotine({ eventOnChange: 'guillotinechange', height: 200 });

                    // Display inital data
                    var data = picture.guillotine('getData');
                    for (var key in data) { $('#' + key).html(data[key]); }

                    // Bind button actions
                    $('#rotate_left').click(function() { picture.guillotine('rotateLeft'); });
                    $('#rotate_right').click(function() { picture.guillotine('rotateRight'); });
                    $('#fit').click(function() { picture.guillotine('fit'); });
                    $('#zoom_in').click(function() { picture.guillotine('zoomIn'); });
                    $('#zoom_out').click(function() { picture.guillotine('zoomOut'); });

                    // Update data on change
                    picture.on('guillotinechange', function(ev, data, action) {
                        data.scale = parseFloat(data.scale.toFixed(4));
                        for (var k in data) { $('#' + k).html(data[k]); }
                    });
                    picture.guillotine('fit');
                });


                // Make sure the 'load' event is triggered at least once (for cached images)
                if (picture.prop('complete')) picture.trigger('load')



                // var imgGrande = $("#urlImg").val();
                // console.log(imgGrande)
                // var iv1 = $("#viewer").iviewer({
                //     src: "http://quinsac.patrimoniocultural.gob.cl/sites/default/original_image_files/LFD1655/LFD1655.01.JPG",
                //     update_on_resize: false,
                //     zoom_animation: false,
                //     mousewheel: false,
                //     onMouseMove: function(ev, coords) {},
                //     onStartDrag: function(ev, coords) { return true; }, //this image will not be dragged
                //     onDrag: function(ev, coords) {}
                // });

                // $("#in").click(function() { iv1.iviewer('zoom_by', 1); });
                // $("#out").click(function() { iv1.iviewer('zoom_by', -1); });
                // $("#fit").click(function() { iv1.iviewer('fit'); });
                // $("#orig").click(function() { iv1.iviewer('set_zoom', 100); });
                // $("#update").click(function() { iv1.iviewer('update_container_info'); });


                // $("#chimg").click(function() {
                //     iv2.iviewer('loadImage', imgGrande);
                //     return false;
                // });

                // var fill = false;
                // $("#fill").click(function() {
                //     fill = !fill;
                //     iv2.iviewer('fill_container', fill);
                //     return false;
                // });






            });




        }
    };

})(jQuery, Drupal);