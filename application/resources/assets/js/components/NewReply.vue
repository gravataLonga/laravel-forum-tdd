<template>
	<div>
		<div v-if="isSigned">
		    <div class="form-group">
		        <textarea 
		        	name="body" 
		        	id="" 
		        	rows="5" 
		        	class="form-control" 
		        	placeholder="Have something to say?"
		        	required
		        	v-model="body"></textarea>
		    </div>

		    <button class="btn btn-default" @click="addReply">Reply</button> 
		</div>

		<div v-else>
			<p class="text-center">
				Please <a href="/login">Sign in</a> for participate on thread.
			</p>
		</div>
	</div>
</template>

<script>
	export default {
		
		data () {
			return {
				body: ''
			}
		},

		computed: {
			isSigned() {
				return window.App.signedIn;
			}
		},

		methods: {
			addReply() {
				axios.post(location.pathname + '/replies', { body: this.body})
					.then(response => {
						this.body = ''; 
						flash('You reply was posted');
						this.$emit('created', response.data);
					})
					.catch(error => {
						flash(error.response.data, 'danger')
					})
			}
		}
	}
</script>