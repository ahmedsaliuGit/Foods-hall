<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <textarea rows="5" class="form-control" 
                    name="body" id="body"
                    placeholder="Have something to say?" 
                    required v-model="body"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success btn-block" @click="addReply">
                    Reply to post
                </button>
            </div>
        </div>

        <div v-else>
            <p class="text-center">You need to <a href="/login">sign in</a> before you can share your thougths</p>
        </div>
    </div>
</template>

<script>
import 'jquery.caret';
import 'at.js';

export default {

    data() {
        return {
            body: ''
        }
    },

    mounted() {
        $("#body").atwho({
            at: '@',
            delay: 750,
            callbacks: {
                remoteFilter: function(query, callback){
                    $.getJSON('/api/users', {q: query}, function(usernames) {
                        callback(usernames);
                    });
                }
            }
        });
    },

    methods: {
        addReply() {
            axios.post(location.pathname + '/replies', {body: this.body})
                .catch( error => {
                    flash(error.response.data, 'danger');
                })
                .then(({data}) => {
                    this.body = '';

                    flash('Reply has been posted');

                    this.$emit('created', data);
                });
        }
    }
}

</script>