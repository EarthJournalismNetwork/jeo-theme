import "./font-size";

window.addEventListener("DOMContentLoaded", function () {
    jQuery(window).scroll(function () {
        var headerHeight = document.querySelector(
            ".bottom-header-contain.desktop-only"
        ).offsetTop;
        // console.log(headerHeight);
        if (jQuery(this).scrollTop() > headerHeight) {
            jQuery(".bottom-header-contain.post-header").addClass("active");

            if (!jQuery("header #header-search").hasClass("fixed")) {
                jQuery("header #header-search").addClass("fixed");
                jQuery("header #header-search").css("top", 50 + "px");
                jQuery("header #header-search").css(
                    "height",
                    jQuery(window).height() - 50 + "px"
                );
            }
        } else {
            if (!jQuery("body").hasClass("mobile-menu-opened")) {
                jQuery(".bottom-header-contain.post-header").removeClass("active");
            }

            if (jQuery("header #header-search").hasClass("fixed")) {
                jQuery("header #header-search").removeClass("fixed");
                jQuery("header #header-search").css(
                    "top",
                    document.querySelector(".bottom-header-contain.desktop-only")
                        .offsetTop +
                    50 +
                    "px"
                );
                jQuery("header #header-search").css(
                    "height",
                    jQuery(window).height() -
                    document.querySelector(".bottom-header-contain.desktop-only")
                        .offsetTop +
                    "px"
                );
            }
        }
    });

    jQuery("button.search-toggle").click(function (e) {
        jQuery("header#masthead").toggleClass("hide-header-search");
    });

    jQuery("header #header-search").css(
        "top",
        document.querySelector(".bottom-header-contain.desktop-only").offsetTop +
        50 +
        "px"
    );
    jQuery("header #header-search").css(
        "height",
        jQuery(window).height() -
        document.querySelector(".bottom-header-contain.desktop-only").offsetTop +
        "px"
    );

    document
        .getElementById("mobile-sidebar-fallback")
        .style.setProperty(
            "--padding-left",
            jQuery(
                ".bottom-header-contain.post-header .mobile-menu-toggle.left-menu-toggle"
            ).offset().left + "px"
        );

    jQuery(".more-menu--content").css(
        "left",
        jQuery("aside#mobile-sidebar-fallback").offset().left +
        jQuery("aside#mobile-sidebar-fallback").width() +
        jQuery(
            ".bottom-header-contain.post-header .mobile-menu-toggle.left-menu-toggle"
        ).offset().left
    );

    jQuery("button.mobile-menu-toggle").click(function () {
        jQuery(".more-menu--content").css(
            "left",
            jQuery("aside#mobile-sidebar-fallback").width()
        );
    });

    jQuery('button[action="toggle-options"]').click(function () {
        jQuery(this.parentNode.querySelector(".toggle-options")).toggleClass(
            "active"
        );
    });

    const shareData = {
        title: document.title,
        text: "",
        url: document.location.href,
    };

    const btn = document.querySelector('button[action="share-navigator"]');
    const resultPara = document.querySelector("body");

    if(document.location.protocol != 'http:') {
        btn.addEventListener("click", () => {
            try {
                navigator.share(shareData);
            } catch (err) {
                resultPara.textContent = "Error: " + err;
            }
        });
    } else {
        console.log("Native share is not allowed over HTTP protocol.")
    }
});