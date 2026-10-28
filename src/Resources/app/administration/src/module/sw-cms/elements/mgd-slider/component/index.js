import template from './sw-cms-el-mgd-slider.html.twig';

Shopware.Component.register('sw-cms-el-mgd-slider', {
    template,

    mixins: [
        Shopware.Mixin.getByName('cms-element'),
    ],

    computed: {
        sliderId() {
            return this.element?.config?.sliderId?.value || '';
        },
    },

    created() {
        this.initElementConfig('mgd-slider');
    },
});
