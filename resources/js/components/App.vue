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
                    :disabled="loading"
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

        <div v-if="userStats.headers.length > 0">
            <table class="table table-striped table-bordered table-hover table-sm table-responsive">
                <thead>
                <tr>
                    <th scope="col" v-for="header in userStats.headers">{{ header }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(items) in userStats.items">
                    <td v-for="item in items">{{ item }}</td>
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
            userStats: {
                headers: [],
                items: [],
            }
        };
    },

    mounted() {
        this.focusInput();
    },

    methods: {
        getPlays() {
            if (this.loading) {
                return;
            }

            if (!this.userName) {
                alert('Введите имя пользователя');
                return;
            }

            this.loading = true;
            this.userStats.headers = [];

            api.plays.get(this.userName)
                .then((response) => {
                    this.userStats = response.data.stats;
                    if (this.userStats.headers.length === 0) {
                        alert('Записей не найдено');
                    }
                })
                .catch((err) => alert(err.response.data.error))
                .finally(() => this.loading = false);
        },

        focusInput() {
            this.$refs.username.focus();
        }
    }
}
</script>
