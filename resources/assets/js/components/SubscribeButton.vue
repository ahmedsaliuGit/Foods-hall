<template>
    <button :class="classes" @click="subscribe" v-text="word"></button>
</template>

<script>
    export default {
        props: ['active'],

        data() {
            return {
                word: 'Subscribe'
            }
        },

        computed: {
            classes (){
                return [ 'btn', this.active ? 'btn-primary' : 'btn-default' ];
            }
        },

        methods: {
            subscribe() {
                axios[(this.active ? 'delete' : 'post')](location.pathname + '/subscriptions');

                this.active = !this.active;

                this.word = this.word === 'Subscribe' ? 'Unsubscribe' : 'Subscribe';

                flash('Subscriptio activated');
            }
        }
    }
</script>