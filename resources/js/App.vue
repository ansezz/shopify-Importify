<template>
    <el-container>
        <el-header>
            <el-menu :default-active="activeIndex" class="el-menu-demo" mode="horizontal"
                     @select="handleSelect"
                     :router="true">
                <el-menu-item v-for="menu in menus" :index="menu.index" :route="menu.route" :key="menu.index">
                    {{menu.name}}
                </el-menu-item>
            </el-menu>
        </el-header>
        <el-main>
            <router-view></router-view>
        </el-main>
        <el-footer>
            footer
        </el-footer>
    </el-container>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                activeIndex: '1',
                count: 0,
                menus: [
                    {
                        route: '/',
                        index: '1',
                        name: 'ImportifyApp'
                    },
                    {
                        route: '/',
                        index: '2',
                        name: 'Home'
                    },
                    {
                        route: '/page/help',
                        index: '3',
                        name: 'Help'
                    }
                ]
            }
        },
        methods: {
            clickMe() {
                this.count++;
            },
            handleSelect(key, keyPath) {
                console.log(key, keyPath);
            }
        }
    }
</script>
