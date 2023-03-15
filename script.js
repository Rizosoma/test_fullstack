function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

class StringStorage {
    constructor() {
        this.strings = [];
    }

    init() {
        return sleep(1000)
            .then(() => {
                for (let i = 0; i < 500; i++) {
                    this.strings.push(Math.random().toString(36).substring(7));
                }
            })
            .then(() => sleep(1000));
    }

    get() {
        return this.strings;
    }
}

window.onload = () => {
    const storage = new StringStorage();
    storage.init().then(() => {
        const strings = storage.get();
        const $content = $('#content');

        strings.forEach((str, index) => {
            $content.append(`<div id="element-${index + 1}">${str}</div>`);
        });

        function scrollToElement(id) {
            const $target = $(`#${id}`);
            const menuHeight = $('.menu').outerHeight();
            $('html, body').animate({ scrollTop: $target.offset().top - menuHeight }, 500);
        }

        $('#first').on('click', () => {
            scrollToElement("element-1");
        });

        $('#last').on('click', () => {
            scrollToElement(`element-${strings.length}`);
        });

        $('#go').on('click', () => {
            const index = parseInt($('#index').val(), 10);
            if (index >= 0 && index < strings.length) {
                scrollToElement(`element-${index}`);
            }
        });
    });
};