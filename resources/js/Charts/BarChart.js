import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
    extends: Bar,
    mixins: [reactiveProp],
    props: ['options'],
    mounted() {
        // this.chartData is created in the mixin.
        // If you want to pass options please create a local options object
        this.renderChart(
            this.chartData,
            {
                responsive: true,
                maintainAspectRatio: false,
                height: this.height,
                legend: {
                    display: false
                },
                tooltips: {
                     callbacks: {
                        title: function(t, d) {
                           return d.labels[t[0].index];
                        },
                        label: function(tooltipItem, data) {
                          return 'Total score - '+ data.datasets[0].data[tooltipItem.index] +'%';
                        }
                     }
                },
                // layout: {
                //     padding: 25
                // },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: true
                        },

                        ticks: {
                            min: 0,           
                            max: 100,
                            stepSize: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return value+'%';
                            }
                        }
                    }]
                }
            }
        )
    }
}