//&lt;![CDATA[
jQuery(document).ready(function ($) {
    var checkRun = true;
    var checkWidths = $(window).width();

    function inViews() {
        var homeoutcap = $('#home-content-out-cap');

        if (homeoutcap.length) {
            var bottom_of_object = $('#home-content-out-cap').offset().top;
            var bottom_of_window = $(window).scrollTop() + $(window).height();
            if ((bottom_of_window > bottom_of_object) && (checkWidths > 767)) {
                return true;
            }

        } else {
            return false;
        }
    };
    if (checkWidths < 768) {
        var doughnutData = [{
                value: 95,
                color: "#e92890"
            },
            {
                value: 100 - 95,
                color: "rgba(0,0,0,0)"
            }
        ];

        $("#myDoughnut").doughnutit({
            dnData: doughnutData,
            dnSize: 187,
            dnInnerCutout: 90,
            dnAnimation: true,
            dnAnimationSteps: 60,
            dnAnimationEasing: 'linear',
            dnStroke: false,
            dnShowText: true,
            dnFontSize: '30px',
            dnFontColor: "#e92890",
            dnText: '95%',
            dnFontOffset: 20,
            dnStartAngle: 90,
            dnCounterClockwise: false,
        }); // End Doughnut
        var doughnutData = [{
                value: 90,
                color: "#fbc443"
            },
            {
                value: 100 - 90,
                color: "rgba(0,0,0,0)"
            }
        ];
        $("#myDoughnut2").doughnutit({
            dnData: doughnutData,
            dnSize: 187,
            dnInnerCutout: 90,
            dnAnimation: true,
            dnAnimationSteps: 60,
            dnAnimationEasing: 'linear',
            dnStroke: false,
            dnShowText: true,
            dnFontOffset: 20,
            dnFontSize: '30px',
            dnFontColor: "#fbc443",
            dnText: '90%',
            dnStartAngle: 90,
            dnCounterClockwise: false,
        });
        var doughnutData = [{
                value: 85,
                color: "#25bce9"
            },
            {
                value: 100 - 85,
                color: "rgba(0,0,0,0)"
            }
        ];
        $("#myDoughnut3").doughnutit({
            dnData: doughnutData,
            dnSize: 187,
            dnInnerCutout: 90,
            dnAnimation: true,
            dnAnimationSteps: 60,
            dnAnimationEasing: 'linear',
            dnStroke: false,
            dnFontOffset: 20,
            dnShowText: true,
            dnFontSize: '30px',
            dnFontColor: "#25bce9",
            dnText: '85%',
            dnStartAngle: 90,
            dnCounterClockwise: false,
        });
        var doughnutData = [{
                value: 80,
                color: "#94eae3"
            },
            {
                value: 100 - 80,
                color: "rgba(0,0,0,0)"
            }
        ];
        $("#myDoughnut4").doughnutit({
            dnData: doughnutData,
            dnSize: 187,
            dnInnerCutout: 90,
            dnAnimation: true,
            dnAnimationSteps: 60,
            dnFontOffset: 20,
            dnAnimationEasing: 'linear',
            dnStroke: false,
            dnShowText: true,
            dnFontSize: '30px',
            dnFontColor: "#94eae3",
            dnText: '80%',
            dnStartAngle: 90,
            dnCounterClockwise: false,
        });

    }

    function inView() {
        var b = inViews();
        if (b == true && checkRun == true) {
            checkRun = false;
            var doughnutData = [{
                    value: 95,
                    color: "#fd5b4e"
                },
                {
                    value: 100 - 95,
                    color: "rgba(0,0,0,0)"
                }
            ];
            $("#myDoughnut").doughnutit({
                dnData: doughnutData,
                dnSize: 187,
                dnInnerCutout: 90,
                dnAnimation: true,
                dnAnimationSteps: 60,
                dnAnimationEasing: 'linear',
                dnStroke: false,
                dnShowText: true,
                dnFontSize: '24px',
                dnFontColor: "#fd5b4e",
                dnText: '95%',
                dnFontOffset: 20,
                dnStartAngle: 90,
                dnCounterClockwise: false,
            }); // End Doughnut
            var doughnutData = [{
                    value: 90,
                    color: "#ffa63e"
                },
                {
                    value: 100 - 90,
                    color: "rgba(0,0,0,0)"
                }
            ];
            $("#myDoughnut2").doughnutit({
                dnData: doughnutData,
                dnSize: 187,
                dnInnerCutout: 90,
                dnAnimation: true,
                dnAnimationSteps: 60,
                dnAnimationEasing: 'linear',
                dnStroke: false,
                dnShowText: true,
                dnFontOffset: 20,
                dnFontSize: '24px',
                dnFontColor: "#ffa63e",
                dnText: '90%',
                dnStartAngle: 90,
                dnCounterClockwise: false,
            });
            var doughnutData = [{
                    value: 85,
                    color: "#25bce9"
                },
                {
                    value: 100 - 85,
                    color: "rgba(0,0,0,0)"
                }
            ];
            $("#myDoughnut3").doughnutit({
                dnData: doughnutData,
                dnSize: 187,
                dnInnerCutout: 90,
                dnAnimation: true,
                dnAnimationSteps: 60,
                dnAnimationEasing: 'linear',
                dnStroke: false,
                dnFontOffset: 20,
                dnShowText: true,
                dnFontSize: '24px',
                dnFontColor: "#25bce9",
                dnText: '85%',
                dnStartAngle: 90,
                dnCounterClockwise: false,
            });
            var doughnutData = [{
                    value: 80,
                    color: "#5cc99f"
                },
                {
                    value: 100 - 80,
                    color: "rgba(0,0,0,0)"
                }
            ];
            $("#myDoughnut4").doughnutit({
                dnData: doughnutData,
                dnSize: 187,
                dnInnerCutout: 90,
                dnAnimation: true,
                dnAnimationSteps: 60,
                dnFontOffset: 20,
                dnAnimationEasing: 'linear',
                dnStroke: false,
                dnShowText: true,
                dnFontSize: '24px',
                dnFontColor: "#5cc99f",
                dnText: '80%',
                dnStartAngle: 90,
                dnCounterClockwise: false,
            });
            b = false;
        }
    };
    $(window).on('scroll', function () {
        inView();
    });
});
//]]&gt;