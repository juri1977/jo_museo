window.onload = function () {
    var json = {
            properties: {
                canvas: {
                    overview: {
                        enabled: true
                    }
                },
                mobile: false,
                text: {
                    enabled: false
                },
                doctype: "manifest",
                lang: "de",
                maximalPageScale: 1,
                permalink: {
                  updateHistory: false
                },
                i18nURL: '/typo3conf/ext/jo_museo/Resources/Public/i18n/{lang}.json',
                webApplicationBaseURL: location.href.substr(0,location.href.lastIndexOf('/')),
                imageAPIURL: $(".mycore-viewer").attr("iiif-imageapi"),
                manifestURL: $(".mycore-viewer").attr("iiif-manifesturl"),
                filePath: $(".mycore-viewer").attr("iiif-filepath"),
                spinnerPath: '/typo3conf/ext/jo_museo/Resources/Public/Images/spinner.gif'
            }
      };
    new mycore.viewer.MyCoReViewer(jQuery(".mycore-viewer"), json.properties);
};
