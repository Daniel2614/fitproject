<template>
    <div class="form-inline">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control" v-model="filterText" @keyup.enter="doFilter" placeholder="Nombre, email, nickname">
            </div>
            <button class="btn btn-info btn-flat" @click="doFilter">Buscar</button>
            <button class="btn btn-flat" @click="resetFilter">Limpiar</button>
        </div>
        <br/><br/>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                filterText: ''
            }
        },
        methods: {
            doFilter () {
                this.$eventHub.$emit('filter-set', this.filterText);
            },
            resetFilter () {
                this.filterText = '';
                this.$eventHub.$emit('filter-reset');
            }
        }
    }
</script>
