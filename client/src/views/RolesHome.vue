<template>
  <v-container>
    <v-card min-width="900" class="">
      <v-card-title>Horarios</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12">
            <v-row>
              <v-col cols="1">
                Numero
              </v-col>
              <v-col>
                UNIDAD
              </v-col>
              <v-col >
                Vueltas
              </v-col>
            </v-row>
            <v-row v-for="(dato, i) in datos" :key="i">
              <v-col cols="1">
                {{dato.rol}}
              </v-col>
              <v-col cols="1">
                {{dato.unidad}}
              </v-col>
              <v-col cols="10">
                <v-row no-gutters dense>
                  <v-col v-for="(sub, x) in dato.corridas" :key="x">
                    <v-row>Vuelta {{sub.numero}}</v-row>
                    <v-divider></v-divider>
                    <v-row dense no-gutters>
                      <v-col>
                        <p>{{sub.ida.salida.parada}}</p>
                      </v-col>
                      <v-col>
                        <p>{{sub.ida.llegada.parada}}</p>
                      </v-col>
                      <v-divider vertical inset></v-divider>
                      <v-col>
                        <p>{{sub.regreso.salida.parada}}</p>
                      </v-col>
                      <v-divider vertical inset></v-divider>
                      <v-col>
                        <p>{{sub.regreso.llegada.parada}}</p>
                      </v-col>
                    </v-row>
                    <v-row dense no-gutters>
                      <v-col cols="3">
                        <p>{{sub.ida.salida.hora}}</p>
                      </v-col>
                      <v-col cols="3">
                        <p>{{sub.ida.llegada.hora}}</p>
                      </v-col>
                      <v-col cols="3">
                        <p>{{sub.regreso.salida.hora}}</p>
                      </v-col>
                      <v-col cols="3">
                        <p>{{sub.regreso.llegada.hora}}</p>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
        <!--<v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th>
                  #
                </th>
                <th>
                  U
                </th>
                <th>
                  Vueltas
                </th>
              </tr>
            </thead>
            <tbody v-for="(dato, i) in datos" :key="i">
              <tr>
                <td>
                  {{dato.rol}}
                </td>
                <td>
                  {{dato.unidad}}
                </td>
                <td>
                  <v-container>
                    <v-col cols="12">
                      <v-row no-gutters dense>
                        <v-col v-for="(sub, x) in dato.corridas" :key="x">
                          <v-row>Vuelta {{sub.numero}}</v-row>
                          <v-divider></v-divider>
                          <v-row dense no-gutters>
                            <v-col>
                              <p>{{sub.ida.salida.parada}}</p>
                            </v-col>
                            <v-divider vertical inset></v-divider>
                            <v-col>
                              <p>{{sub.ida.llegada.parada}}</p>
                            </v-col>
                            <v-divider vertical inset></v-divider>
                            <v-col>
                              <p>{{sub.regreso.salida.parada}}</p>
                            </v-col>
                            <v-divider vertical inset></v-divider>
                            <v-col>
                              <p>{{sub.regreso.llegada.parada}}</p>
                            </v-col>
                          </v-row>
                          <v-row dense no-gutters>
                            <v-col cols="3">
                              <p>{{sub.ida.salida.hora}}</p>
                            </v-col>
                            <v-col cols="3">
                              <p>{{sub.ida.llegada.hora}}</p>
                            </v-col>
                            <v-col cols="3">
                              <p>{{sub.regreso.salida.hora}}</p>
                            </v-col>
                            <v-col cols="3">
                              <p>{{sub.regreso.llegada.hora}}</p>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-container>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>-->
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  beforeMount(){
    if(this.logeado == null && this.sesion == false){
        this.$router.push('login')
    }
  },
  data: () => ({
    datos: [
      {
        rol: "1",
        unidad: "UNIDAD 101",
        corridas: 
        [
          {
            numero: 1,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 2,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 3,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 4,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 5,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
        ]
      },
      {
        rol: "2",
        unidad: "UNIDAD 122",
        corridas: 
        [
          {
            numero: 1,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 2,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          },
          {
            numero: 3,
            ida: 
            {
              salida: 
              {
                parada: "VAL",
                hora: "10:00",
              },
              llegada:
              {
                parada: "SAJ",
                hora: "11:30",
              }
            },
            regreso:
            {
              salida: 
              {
                parada: "SAJ",
                hora: "12:00",
              },
              llegada:
              {
                parada: "VAL",
                hora: "13:30",
              }
            }
          }
        ]
      },
    ]
  }),

  computed: {
    ...mapGetters({
      logeado: 'logdata/getKey',
      sesion: 'logdata/getSucess',
    })
  }
}
</script>

<style>

</style>