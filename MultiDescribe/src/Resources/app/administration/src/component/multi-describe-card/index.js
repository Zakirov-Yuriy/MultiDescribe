import template from './multi-describe-card.html.twig';
import './multi-describe-card.scss';

Shopware.Component.register('multi-describe-card', {
    template,

    inject: ['repositoryFactory'],

    props: {
        product: {
            type: Object,
            required: true,
        },
        salesChannels: {
            type: Array,
            required: true,
        }
    },

    computed: {
        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },
    },

    data() {
        return {
            customFieldSet: null,
        };
    },

    created() {
        this.loadCustomFieldSet();
    },

    methods: {
        loadCustomFieldSet() {
            const criteria = new Shopware.Data.Criteria();
            criteria.addFilter(Shopware.Data.Criteria.equals('name', 'multi_describe_set'));
            criteria.addAssociation('customFields');

            this.customFieldSetRepository.search(criteria, Shopware.Context.api).then(result => {
                this.customFieldSet = result.first();
            });
        },

        getFieldName(languageId, salesChannelId) {
            // This logic needs to be more robust, matching the PHP service
            return `description_${languageId}_${salesChannelId}`;
        }
    }
});
