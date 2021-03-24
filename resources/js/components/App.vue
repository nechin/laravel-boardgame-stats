<template>
    <div class="col-md-12">
        <h4>Матрица</h4>
        <div>
            <label>Введите матрицу</label>
        </div>
        <div>
            <textarea
                v-model="matrix"
            >
            </textarea>
        </div>
        <div>
            <div>Сумма главной диагонали: {{ sumBase }}</div>
        </div>
        <div>
            <div>Сумма побочной диагонали: {{ sumSide }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                matrix: '',
            };
        },

        computed: {
            sumBase: function () {
                let result = 0;
                let aMatrix = [];
                let tempMatrix = this.matrix.split("\n");

                tempMatrix.forEach(function(item){
                    aMatrix.push(item.replace(/[^\d\s]/gi, '').split(" "))
                })

                //result = sum;

                return result;
            },
            sumSide: function () {
                let result = 0;

                return result;
            }
        },
    }
</script>
