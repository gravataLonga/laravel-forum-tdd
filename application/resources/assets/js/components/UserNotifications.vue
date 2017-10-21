<template>
	<li class="dropdown" v-if="notifications.length">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<span class="glyphicon glyphicon-bell"></span>
		</a>
		<ul class="dropdown-menu">
			<li v-for="notifcation in notifications">
				<a :href="notifcation.data.link" v-text="notifcation.data.message" @click.prevent="markAsRead(notifcation)"></a>
			</li>
		</ul>
	</li>
</template>

<script>
	export default {
		data() {
			return {
				notifications: false
			};
		},

		created() {
			axios.get('/profile/'+window.App.user.name+'/notifications').then(response => this.notifications = response.data);
		},

		methods: {
			markAsRead(notifcation) {
				axios.delete('/profile/'+window.App.user.name+'/notifications/' + notifcation.id).then(response => this.notifications = response.data);
			}
		}
	}
</script>