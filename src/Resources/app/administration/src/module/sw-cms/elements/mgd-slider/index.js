import './component';
import './config';

// Element anmelden
Shopware.Service('cmsService').registerCmsElement({
    name: 'mgd-slider',
    label: 'sw-cms.elements.mgd-slider.label',
    component: 'sw-cms-el-mgd-slider',
    configComponent: 'sw-cms-el-config-mgd-slider',
    defaultConfig: {
        sliderId: {
            source: 'static',
            value: '',
        },
    },
});
