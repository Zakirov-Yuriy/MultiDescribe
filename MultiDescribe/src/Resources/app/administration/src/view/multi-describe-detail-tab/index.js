import template from './multi-describe-detail-tab.html.twig';

Shopware.Component.register('multi-describe-detail-tab', {
    template,

    inject: ['repositoryFactory'],

    computed: {
        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
    },

    data() {
        return {
            salesChannels: [],
        };
    },

    created() {
        this.loadSalesChannels();
    },

    methods: {
        loadSalesChannels() {
            this.salesChannelRepository.search(new Shopware.Data.Criteria(), Shopware.Context.api).then(result => {
                this.salesChannels = result;
            });
        }
    }
});
