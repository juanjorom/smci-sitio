<template>
    <v-container>
        <v-card>
            <v-label>{{datos.propiedad}}</v-label>
            <v-label>{{datos.cantidad}}</v-label>
        </v-card>
    </v-container>
</template>

<script>
export default {
    props: {
        datos: Object
    }
}
</script>

<style>

</style>