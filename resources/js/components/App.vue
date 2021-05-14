<template>
    <div class="col-md-12">
        <p class="font-italic">Данная страница показывает последние {{ countPlays }} сыгранных партий в игры из коллекции пользователя по месяцам</p>
        <p class="font-weight-bold text-danger font-small">
            Внимание! Если у пользователя в коллекции более 400 игр, то статистика не будет показана. К сожалению, boardgamegeek.com не может обслуживать такие данные.
        </p>
        <div class="input-group mb-3">
            <input
                v-model="userName"
                ref="username"
                type="text"
                class="form-control"
                placeholder="Username пользователя с boardgamegeek.com"
                aria-label="Username пользователя с boardgamegeek.com"
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
            <table class="table table-striped table-bordered table-hover table-sm table-responsive font-small">
                <thead>
                <tr>
                    <th scope="col" v-for="header in userStats.headers">
                        <span class="vertical">{{ header[0] }}</span>
                    </th>
                </tr>
                <tr>
                    <th scope="col" v-for="header in userStats.headers">{{ header[1] }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(items, itemsIndex) in userStats.items">
                    <td v-for="(item, rowIndex) in items">
                        <span :class="getLastPlayClass(itemsIndex, rowIndex)">{{ item }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div>
                <span class="more-half-year font-small">* Не игралась больше полугода</span><br>
                <span class="more-year font-small">* Не игралась больше года</span>
            </div>
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
                props: [],
            }
        };
    },

    mounted() {
        this.focusInput();
    },

    computed: {
        countPlays() {
            return 2000;
        }
    },

    methods: {
        getLastPlayClass(itemsIndex, rowIndex) {
            if (rowIndex > 0 || typeof this.userStats.props[itemsIndex] === 'undefined') {
                return "";
            }

            if (this.userStats.props[itemsIndex].year === true) {
                return "more-year";
            } else if (this.userStats.props[itemsIndex].half) {
                return "more-half-year";
            }

            return "";
        },

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

<style scoped>
    th {
        text-align: center;
        padding: 4px 2px 0;
    }

    span.vertical {
        writing-mode: vertical-rl;
        text-orientation: upright;
    }

    td {
        padding: 1px 2px;
    }

    .font-small {
        font-size: .8em;
    }

    span.more-year {
        color: red;
    }
    span.more-half-year {
        color: #b45400;
    }
</style>
