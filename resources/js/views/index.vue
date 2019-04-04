<template>
    <el-row>
        <el-col :span="24">
            <form @submit.prevent="submit()">
                <small class="form-text text-muted">Product / Collection / Store URL.</small>
                <el-input placeholder="URL" v-model="url" class="input-with-button" :disabled="loading">
                    <el-button native-type="submit" slot="append" icon="el-icon-search" :loading="loading"></el-button>
                </el-input>
            </form>
        </el-col>
        <el-col :span="24">
            <el-row>
                <el-col :span="8"
                        v-for="(item, index) in products"
                        :key="index"
                        :offset="index % 2 === 0 ? 0 : 2"
                        v-loading="item.loading"
                >
                    <el-card :body-style="{ padding: '0px' }">
                        <div slot="header" class="clearfix">
                            <span>{{item.title}}</span>
                        </div>
                        <img :src="getImageSrc(item)" class="image" :alt="item.title">
                        <div style="padding: 14px;">
                            <div class="bottom clearfix">
                                <el-button v-if="!item.imported" type="danger" size="mini" class="button"
                                           icon="el-icon-download"
                                           @click="importer(item)">
                                    Importer
                                </el-button>
                                <el-button v-else type="success" size="mini" class="button" icon="el-icon-upload"
                                           @click="view(shop,item.imported.handle)">
                                    View Imported
                                </el-button>
                                <el-button type="info" size="mini" class="button"
                                           @click="view(result.url.origin ,item.handle)">
                                    View
                                </el-button>
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>
            <el-row>
                <el-button v-if="products.length > 1 && !loading && !no_more" @click="loadMore()" type="primary"
                           icon="el-icon-search">
                    Load More Products
                </el-button>
                <p v-if="no_more">No more Products</p>
            </el-row>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        mounted() {
        },
        data() {
            return {
                shop: 'https://' + window.shop,
                loading: false,
                no_more: false,
                url: '',
                page: 1,
                result: [],
                products: [],
            }
        },
        methods: {
            submit() {
                this.page = 1;
                this.no_more = false;
                this.products = [];
                this.getResult()
            },
            loadMore() {
                this.page++;
                this.getResult();
            },
            getResult() {
                this.loading = true;
                this.$API.search(this.url, this.page)
                    .then((response) => {
                        if (response.data.result.product) {
                            response.data.result.product.loading = false;
                            response.data.result.product.imported = null;
                            this.products.push(response.data.result.product)
                        } else if (response.data.result.products) {
                            if (response.data.result.products.length === 0)
                                this.no_more = true;

                            response.data.result.products.map((item, index) => {
                                response.data.result.products[index].loading = false
                                response.data.result.products[index].imported = null;
                            });
                            if (this.page > 1) {
                                this.products = [...this.products, ...response.data.result.products];
                            } else {
                                this.products = response.data.result.products
                            }
                        }
                        console.log(this.products)
                        this.result = response.data.result;
                        this.result.url = new URL(response.data.url);
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            importer(product) {
                product.loading = true;
                this.$API.importer(product.handle, this.result.url)
                    .then((response) => {
                        product.imported = response.data.product
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    product.loading = false;
                });
            },
            getImageSrc(item) {
                if (item.image)
                    return item.image.src;

                if (item.images)
                    return item.images[0].src;

                return 'http://wiringpro.com/wp-content/uploads/2015/10/default-product-image-510x600.jpg';
            },
            view(domain, slug) {
                this.openInNewTab(domain + '/products/' + slug)
            },
            openInNewTab(url) {
                let win = window.open(url, '_blank');
                win.focus();
            }
        }
    };
</script>
<style>
    .time {
        font-size: 13px;
        color: #999;
    }

    .bottom {
        margin-top: 13px;
        line-height: 12px;
    }

    .button {
        padding: 0;
        float: right;
    }

    .image {
        width: 100%;
        display: block;
    }

    .clearfix:before,
    .clearfix:after {
        display: table;
        content: "";
    }

    .clearfix:after {
        clear: both
    }
</style>
