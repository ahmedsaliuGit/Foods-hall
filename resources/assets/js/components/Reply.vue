<template>
    <div  :id="'reply-'+id" class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'profiles/'+reply.owner.name" v-text="reply.owner.name"></a> 
                    said <span v-text="ago"></span> 
                </h5>
                <div v-if="signedIn">    
                    <favorite :reply="reply"></favorite>
                </div>
            </div>
        </div>
        <form @submit="update">
            <div class="panel-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea rows="8" class="form-control" v-model="body" required></textarea>
                    </div>
                    <button class="btn btn-xs btn-primary" type="submit">Update</button>
                    <button class="btn btn-xs btn-link" @click="editing = false" type="button">Cancel</button>
                </div>
                <div v-else v-html="body"></div>
            </div>
        </form>

        <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            </div>
            <button class="btn btn-xs btn-danger ml-a" @click="markBestReply" 
                v-if="authorize('owns', reply.thread)" v-show="! isBest">Best Reply</button>
        </div> 
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: [
            'reply'
        ],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest
            }
        },

        created() {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },

        computed: {
            endpoint() {
                return '/replies/' + this.id;
            },

            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.id, {
                    body: this.body
                }).catch( error => {
                    flash('error.response.data', 'danger');
                });

                this.editing = false;

                this.id;

                flash('Updated!');
            },

            destroy() {
                axios.delete(this.endpoint);

                this.$emit('deleted', this.id);

                //$(this.$el).fadeOut(300, () => {
                //    flash('Deleted!');
                //});
            },

            markBestReply() {
                // this.isBest = true;

                axios.post('/replies/' + this.id + '/best');

                window.events.$emit('best-reply-selected', this.id);
            }
        }
    }
</script>