import template from './sw-cms-el-config-mgd-slider.html.twig';

const { Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-config-mgd-slider', {
    template,

    inject: ['loginService'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            sliders: [],
            loadFailed: false,
        };
    },

    computed: {
        httpClient() {
            return Shopware.Application.getContainer('init').httpClient;
        },
    },

    created() {
        this.initElementConfig('mgd-slider');
        this.loadSliders();
    },

    methods: {
        // Liste der Slider von /api/_action/mgd-slider/list
        async loadSliders() {
            try {
                const response = await this.httpClient.get('/_action/mgd-slider/list', {
                    headers: { Authorization: `Bearer ${this.loginService.getToken()}` },
                });
                this.sliders = response?.data?.data ?? [];
                this.loadFailed = false;
            } catch (e) {
                this.sliders = [];
                this.loadFailed = true;
            }
        },

        onSliderChange(value) {
            this.element.config.sliderId.value = value;
            this.$emit('element-update', this.element);
        },
    },
});
