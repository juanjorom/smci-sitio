<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-row align="center" justify="center">
                    <v-col md="6">
                        <v-card>
                            <v-card-title>
                                <span>Bienvenido</span>
                            </v-card-title>
                            <v-card-text>
                                <v-form ref="form">
                                    <v-text-field v-model="form.user" ref="user" type="text" label="Usuario" :error-messages="getErrors('user')">
                                    </v-text-field>
                                    <v-text-field v-model="form.password" ref="password" type="password" label="Contraseña" :error-messages="getErrors('password')">
                                    </v-text-field>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn @click="validateUser" :disabled="sending">Entrar</v-btn>
                            </v-card-actions>
                            <v-progress-linear v-if="sending" indeterminate></v-progress-linear>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { validationMixin } from 'vuelidate'
import {
    required
  } from 'vuelidate/lib/validators'
export default {
    name: 'login',
    mixins: [validationMixin],
    data: () => ({
        form: {
            user: null,
            password: null,
        }
    }),
    computed: {
        ...mapGetters('logdata',{
            sending: 'getSending',
            getUsers: 'getUsersList',
            succes: 'getSucess',
            rol: 'getRol'
        })
    },
    validations: {
        form:{
            user: {
                required
            },
            password: {
                required,
            }
        }
    },
    methods: {
        ...mapActions('logdata', [
            'log',
        ]),
        getErrors (fieldName){
            const field = this.$v.form[fieldName]

            if (field) {
                const errors =[]
                if(!field.$dirty) return errors
                !field.required && errors.push("Se requiere este campo")
                return errors
            }
        },
        async validateUser () {
            this.$v.$touch()
            if (!this.$v.$invalid) {
                await this.log(this.form)
                if(this.succes){
                    this.$router.push('dashboard')
                }
            }
        }
    }
}
</script>

<style>

</style>