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

            jQuery('#carouselGaleriaMini2').owlCarousel({
                margin: 10,
                dots: false,
                loop: false,
                autoWidth: false,
                nav: true,
                navText: [
                    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                ],
                responsive: {
                    0: {
                        items: 2,
                        nav: true
                    },
                    800: {
                        items: 5,
                        nav: false
                    }
                },
                navContainer: '#main-contentGaleriaMini2 #custom-navMini2'
            })

            jQuery('#carouselGaleria').owlCarousel({
                items: 1,
                loop: false,
                autoWidth: false,
                nav: true,
                dots: false,
                navText: [
                    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                ],
                navContainer: '#main-contentGaleria #custom-nav'
            });

            $('.imagenSlider2').click(function() {
                $('#carouselGaleria').trigger('to.owl.carousel', $(this).attr("id"));
            });

            var owl = $("#carouselGaleria")
            owl.on('changed.owl.carousel', function(e) {
                $('#carouselGaleriaMini2').trigger('to.owl.carousel', e.relatedTarget.current() - 1);
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

            function resetComuna() {
                jQuery("#comuna option").each(function() {
                    $(this).css("display", "block");
                    // alert('opcion ' + $(this).text() + ' valor ' + $(this).attr('value'))
                });
            }
            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop()) {
                    jQuery('#ir-arriba').fadeIn();
                } else {
                    jQuery('#ir-arriba').fadeOut();
                }
            });

            jQuery("#ir-arriba").click(function() {
                jQuery("html, body").animate({ scrollTop: 0 }, 0);
            });
            $(document).ready(function($) {
                $('#region').on('change', function(e) {
                    var valueSelected = this.value;
                    console.log(this.value);
                    resetComuna();
                    $('#comuna').val("0");
                    if (valueSelected != 0) {
                        $("#comuna option").each(function() {
                            if ($(this).val() === "0") {
                                $(this).css("display", "block");
                            } else if ($(this).data("region") != valueSelected) {
                                $(this).css("display", "none");
                            }

                        });
                    }

                });

                $("#btnBuscarBiblioteca").click(function() {
                    // $('.biblioComunas').css("display", "none");
                    // if ($('#comuna').val() === "0") {
                    //     if ($('#region').val() === "0") {
                    //         $('.biblioComunas').css("display", "block");
                    //     } else {
                    //         $(".biblioComunas").each(function() {
                    //             if ($(this).data("regionbiblio") == $('#region').val()) {
                    //                 $(this).css("display", "block");
                    //             }
                    //         });
                    //     }
                    // } else {
                    //     $(".biblioComunas").each(function() {
                    //         if ($(this).data("comunabiblio") == $('#comuna').val()) {
                    //             $(this).css("display", "block");
                    //         }
                    //     });
                    // };
                    $("#btnBuscarBiblioteca").html('Cargando <i class="fas fa-spinner fa-spin"></i>');
                });

            });




        }
    };

})(jQuery, Drupal);