import template from './sw-cms-el-config-mgd-slider.html.twig';

const { Mixin } = Shopware;

const emptySlide = () => ({
    image: '', alt: '', title: '', text: '', buttonLabel: '', buttonUrl: '',
});

Shopware.Component.register('sw-cms-el-config-mgd-slider', {
    template,

    inject: ['loginService'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            sliders: [],
            max: 5,
            loadFailed: false,
            creating: false,
            createError: '',
            createdOk: false,
            // Formular neuer Slider
            form: {
                name: '',
                mode: 'classic',
                autoplay: false,
                source: 'random',
                count: 5,
                slides: [emptySlide()],
            },
        };
    },

    computed: {
        httpClient() {
            return Shopware.Application.getContainer('init').httpClient;
        },

        authHeaders() {
            return { Authorization: `Bearer ${this.loginService.getToken()}` };
        },

        // Zähler Text (Platzhalter selbst ersetzen, $tc mit Plural macht hier Quatsch)
        counterText() {
            return `${this.$tc('sw-cms.elements.mgd-slider.counter')} ${this.sliders.length} / ${this.max}`;
        },

        modeOptions() {
            return [
                { value: 'classic', label: this.$tc('sw-cms.elements.mgd-slider.modeClassic') },
                { value: 'prime', label: this.$tc('sw-cms.elements.mgd-slider.modePrime') },
            ];
        },

        sourceOptions() {
            return [
                { value: 'random', label: this.$tc('sw-cms.elements.mgd-slider.sourceRandom') },
                { value: 'bestseller', label: this.$tc('sw-cms.elements.mgd-slider.sourceBestseller') },
            ];
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
                const response = await this.httpClient.get('/_action/mgd-slider/list', { headers: this.authHeaders });
                this.sliders = response?.data?.data ?? [];
                this.max = response?.data?.max ?? 5;
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

        addSlide() {
            this.form.slides.push(emptySlide());
        },

        removeSlide(index) {
            this.form.slides.splice(index, 1);
        },

        // Neuen Slider anlegen, Limit prüft der Server (deutsche Meldung kommt von dort)
        async createSlider() {
            this.creating = true;
            this.createError = '';
            this.createdOk = false;
            const f = this.form;
            const config = { mode: f.mode, autoplay: f.autoplay };
            if (f.mode === 'prime') {
                config.prime = { source: f.source, count: f.count };
            } else {
                config.slides = f.slides.filter((s) => s.image || s.title || s.text);
            }
            try {
                const response = await this.httpClient.post(
                    '/_action/mgd-slider/create',
                    { name: f.name || 'Slider', config },
                    { headers: this.authHeaders },
                );
                await this.loadSliders();
                this.onSliderChange(response.data.id);
                this.createdOk = true;
            } catch (e) {
                this.createError = e?.response?.data?.errors?.[0]?.detail
                    ?? this.$tc('sw-cms.elements.mgd-slider.createError');
            } finally {
                this.creating = false;
            }
        },
    },
});
