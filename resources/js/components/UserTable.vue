<template>
    <vg-vuetable api-url="/api/v1/users" :fields="fields" :api-token="$attrs.apitoken">
        <span slot="role" slot-scope="props" class="badge bg-purple">{{ props.rowData.role }}</span>

        <template slot="status" slot-scope="props">
            <span v-if="props.rowData.active" class="badge bg-green">Activo</span>
            <span v-else class="badge bg-orange">Inactivo</span>
        </template>

        <img width="50" class="img-circle img-bordered-sm" slot="avatar" slot-scope="props" :src="'/storage/avatars/' + props.rowData.avatar"/>

        <div class="" slot="actions" slot-scope="props">
            <a class="btn btn-flat btn-warning"  @click.prevent="onAction('edit-item', props.rowData)" href="#">
                <i class="fa fa-pencil"></i> Editar
            </a>
            <a class="btn btn-flat btn-danger"  @click.prevent="onAction('delete-item', props.rowData)"href="#">
                <i class="fa fa-trash"></i> Eliminar
            </a>
            <a class="btn btn-flat btn-info"  @click.prevent="onAction('view-item', props.rowData)" href="#">
                <i class="fa fa-eye"></i> Ver
            </a>
        </div>
    </vg-vuetable>
</template>

<script>
    import userFields  from './../field-definitions/userFields'

    export default {
        data() {
            return {
                fields: userFields
            }
        },
        inheritAttrs: false,
        methods: {
            onAction (action, data) {
                alert(`slot action: ' ${action}, ${data.name}, id: ${data.id}`)
            },
        }
    }
</script>
