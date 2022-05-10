(function($, Drupal) {
    'use strict';

    Drupal.behaviors.vpm = {
        attach: function(context, settings) {

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
                        items: 1,
                        nav: true
                    },
                    800: {
                        items: 3,
                        nav: false
                    }
                },
                navContainer: '#main-contentGaleriaMini2 #custom-navMini2'
            })

            jQuery('#carouselGaleriaMini').owlCarousel({
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
                        items: 1,
                        nav: true
                    },
                    800: {
                        items: 3,
                        nav: false
                    }
                },
                navContainer: '#main-contentGaleriaMini #custom-navMini'
            })
            jQuery('#carouselGaleriaMini1').owlCarousel({
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
                        items: 1,
                        nav: true
                    },
                    800: {
                        items: 3,
                        nav: false
                    }
                },
                navContainer: '#main-contentGaleriaMini1 #custom-navMini1'
            })

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

                var urlTwitter = 'https://twitter.com/intent/tweet?text=Conoce "' + name + '" en Portal Monvoisin ' + permalink
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




                // var timeline;
                // timeline = new TL.Timeline('timeline-embed', jsonFile);


                $("#cX").on("change", function() {
                    var value   = $(this).val();
                    var name    = $("#cY");
                    var curso   = name.val();
            
                    if(value == "0") {
                        name.prop("disabled", true);
                    } else {
                        name.prop("disabled", false);
                        /*name.find("option").each(function() {
                            (value == 1) ? 1 : 7
                            var option = $(this).val();
                            var word = option.indexOf(value);
                            (word > 1) ? $(this).show() : $(this).hide()
                        });*/
                     
                    }
                });


                $('#exportPDF').click(function(event) {
                    var reportPageHeight = $('#report').innerHeight();
                    var reportPageWidth = $('#report').innerWidth();


                    var pdfCanvas = $('<canvas />').attr({
                        id: "canvaspdf",
                        width: reportPageWidth,
                        height: reportPageHeight
                    });

                    var pdfctx = $(pdfCanvas)[0].getContext('2d');
                    var pdfctxX = 0;
                    var pdfctxY = 0;
                    var buffer = 100;

                    $("canvas").each(function(index) {
                        var canvasHeight = $(this).innerHeight();
                        var canvasWidth = $(this).innerWidth();

                        pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
                        pdfctxX += canvasWidth + buffer;

                        if (index % 2 === 1) {
                            pdfctxX = 0;
                            pdfctxY += canvasHeight + buffer;
                        }
                    });
                    var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
                    pdf.addImage($(pdfCanvas)[0], 'PNG', 15, 38);
                    pdf.setFontSize(20);
                    pdf.text(50, 50, "Grafico Quinsac en cifras");
                    pdf.save('filename.pdf');
                });













                if (typeof xValues !== 'undefined') {
                    var myData = {
                        labels: xValues,
                        datasets: [{
                            label: Titulo,
                            fill: false,
                            backgroundColor: barColors,
                            borderColor: 'black',
                            data: yValues,
                        }]
                    };

                    var ctx = document.getElementById('my_Chart').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: myData,
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });
                }

                function updateChartType() {

                    myChart.destroy();

                    myChart = new Chart(ctx, {
                        type: document.getElementById("chartType").value,
                        data: myData,
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });
                };

                $("#chartType").change(function() {
                    updateChartType()
                });

                if (typeof jsonFile !== 'undefined') {
                    var timeline;
                    var options = {
                        initial_zoom: 5,
                        hash_bookmark: true,
                        timenav_position: 'top',
                        timenav_height_min: 460,
                        language: 'es',
                        duration: 500,

                    }
                    var timeline = new TL.Timeline('timeline-embed',
                        jsonFile,
                        options);
                }

                $('main', context).once('.tl-timenav').each(function() {
                    $(window).one('load', function() {
                        $('<div class="cronoLeyend pb-4 pt-4"><span class="spanIcon obraLeyend"><i class="fa fa-picture-o" aria-hidden="true"></i></span><span class="textIcon">Obras del catálogo</span><span class="spanIcon"><i class="fa fa-bookmark-o" aria-hidden="true"></i></span><span class="textIcon">Exposiciones</span>                    <span class="spanIcon hitoLeyend"><i class="fa fa-leaf" aria-hidden="true"></i></span><span class="textIcon">Hitos históricos</span>                    <span class="spanIcon destLeyend"><i class="fa fa-star-o" aria-hidden="true"></i></span><span class="textIcon">Destacados</span></div>').insertAfter(".tl-timenav");
                    });
                });


            });




        }
    };

})(jQuery, Drupal);