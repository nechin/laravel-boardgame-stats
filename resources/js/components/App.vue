<template>
    <div class="col-md-12">
        <p class="font-italic">Статистика сыгранных партий для указанного пользователя по давности</p>
        <div class="input-group mb-3">
            <input
                v-model="userName"
                ref="username"
                type="text"
                class="form-control"
                placeholder="Username пользователя"
                aria-label="Username пользователя"
                aria-describedby="basic-addon2"
            >
            <div class="input-group-append">
                <button
                    v-on:click="getPlays"
                    class="btn btn-outline-secondary"
                    type="button"
                >
                    Показать
                </button>
            </div>
        </div>

        <div v-if="loading" class="d-flex justify-content-center m-4">
            <div class="spinner-border" role="status">
                <span class="sr-only">Загрузка...</span>
            </div>
        </div>

        <div v-if="userPlays.length > 0">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Дата</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="play in userPlays">
                    <th scope="row">{{ play.id }}</th>
                    <td>{{ play.name }}</td>
                    <td>{{ play.date }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import api from "../api";

export default {
    data() {
        return {
            loading: false,
            userName: '',
            userPlays: []
        };
    },

    mounted() {
        this.focusInput();
    },

    methods: {
        getPlays() {
            this.loading = true;
            api.plays.get(this.userName)
                .then((response) => {
                    this.userPlays = response.data.plays;
                })
                .catch((error) => alert(error.error))
                .finally(() => this.loading = false);
        },

        focusInput() {
            this.$refs.username.focus();
        }
    }
}
</script>
