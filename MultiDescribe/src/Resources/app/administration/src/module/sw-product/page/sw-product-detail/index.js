Shopware.Component.override('sw-product-detail', {
    computed: {
        productTabs() {
            const tabs = this.$super('productTabs');
            tabs.push({
                name: 'multi-describe',
                label: 'multi-describe.tabTitle',
                component: 'multi-describe-detail-tab'
            });
            return tabs;
        }
    }
});
