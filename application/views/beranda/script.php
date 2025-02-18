<script>
    Array.prototype.max = function() {
      return Math.max.apply(null, this);
    };

    const mghutanglunas = <?= $mghutanglunas ?>;
    const mghutang = <?= $mghutang ?>;
    const mgpiutanglunas = <?= $mgpiutanglunas ?>;
    const mgpiutang = <?= $mgpiutang ?>;

    const mhl = mghutanglunas.max();
    const mh = mghutang.max();
    const mpl = mgpiutanglunas.max();
    const mp = mgpiutang.max();
    let nilai = [mhl, mh, mpl, mp];
    const nilaimax = nilai.max();
    var maxrange = (nilaimax > 0) ? nilaimax : 80;
    var range = (nilaimax > 0) ? (nilaimax/5).toFixed(2) : 20;

    function chartjsBarChartInEx(selector, data1, data2, data3, data4, labels, label = "Bar chart Income Expense") {
      var ctx = document.getElementById(selector);
      if (ctx) {
        ctx.getContext("2d");
        ctx.height = window.innerWidth <= 575 ? 180 : 84;
        var chart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
                data: data1,
                backgroundColor: "#5F63F250",
                hoverBackgroundColor: "#5F63F2",
                label: "Sales In-Cash",
              },
              {
                data: data2,
                backgroundColor: "#FF69A550",
                hoverBackgroundColor: "#FF69A5",
                label: "Sales Out-Cash",
              },
              {
                data: data3,
                backgroundColor: "#FA8B0C50",
                hoverBackgroundColor: "#FA8B0C",
                label: "Purchase In-Cash",
              },
              {
                data: data4,
                backgroundColor: "#20C99750",
                hoverBackgroundColor: "#20C997",
                label: "Purchase Out-Cash",
              },
            ],
          },
          options: {
            maintainAspectRatio: true,
            responsive: true,
            tooltips: {
              mode: "label",
              intersect: false,
              enabled: false,
              custom: customTooltips,
              callbacks: {
                label(t, d) {
                  const dstLabel = d.datasets[t.datasetIndex].label;
                  const {
                    yLabel
                  } = t;
                  return `<span class="chart-data">${yLabel}</span> <span class="data-label">${dstLabel}</span>`;
                },
                labelColor(tooltipItem, chart) {
                  const dataset = chart.config.data.datasets[tooltipItem.datasetIndex];
                  return {
                    backgroundColor: dataset.hoverBackgroundColor,
                    borderColor: "transparent",
                    usePointStyle: true,
                  };
                },
              },
            },
            legend: {
              display: false,
              position: "bottom",
              align: "start",
              labels: {
                boxWidth: 6,
                display: true,
                usePointStyle: true,
              },
            },
            layout: {
              padding: {
                left: "0",
                right: 0,
                top: 0,
                bottom: "0",
              },
            },
            scales: {
              yAxes: [{
                gridLines: {
                  color: "#e5e9f2",
                  borderDash: [3, 3],
                  zeroLineColor: "#e5e9f2",
                  zeroLineWidth: 1,
                  zeroLineBorderDash: [3, 3],
                },
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                  max: maxrange,
                  stepSize: range,
                  callback(value, index, values) {
                    return `${value}jt`;
                  },
                },
              }, ],
              xAxes: [{
                gridLines: {
                  display: false,
                },
                barPercentage: 0.6,
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                },
              }, ],
            },
          },
        });
      }
    }

    $('#tgl').datepicker({
        dateFormat: 'dd-mm-yy',
        firstDay: 1,
        changeMonth: true,
        changeYear: true
    });

    $('#tgl').change(function () {
        var tgl = $('#tgl').val();
        this.form.submit();
    });

    var filtertgl = document.getElementById("form-tgl");
    filtertgl.style.display = 'block';
    // console.log(mghutanglunas, mghutang, mgpiutanglunas, mgpiutang);
    chartjsBarChartInEx(
        "barChartInEx_Why",
        (data = mghutanglunas),
        (data = mghutang),
        (data = mgpiutanglunas),
        (data = mgpiutang),
        labels = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
    );

    $('#incExp-week-tab').on("shown.bs.tab", function () {
        // filtertgl.style.display = 'block';
    });

    const blnhutanglunas = <?= $blnhutanglunas ?>;
    const blnhutang = <?= $blnhutang ?>;
    const blnpiutanglunas = <?= $blnpiutanglunas ?>;
    const blnpiutang = <?= $blnpiutang ?>;

    const bhl = blnhutanglunas.max();
    const bh = blnhutang.max();
    const bpl = blnpiutanglunas.max();
    const bp = blnpiutang.max();
    let nilaibln = [bhl, bh, bpl, bp];
    const nilaiblnmax = nilaibln.max();
    var maxrangebln = (nilaiblnmax > 0) ? nilaiblnmax : 80;
    var rangebln = (nilaiblnmax > 0) ? (nilaiblnmax/5).toFixed(2) : 20;

    function chartjsBarChartInExMonth(selector, data1, data2, data3, data4, labels, label = "Bar chart Income Expense") {
      var ctx = document.getElementById(selector);
      if (ctx) {
        ctx.getContext("2d");
        ctx.height = window.innerWidth <= 575 ? 180 : 84;
        var chart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
                data: data1,
                backgroundColor: "#5F63F250",
                hoverBackgroundColor: "#5F63F2",
                label: "Sales In-Cash",
              },
              {
                data: data2,
                backgroundColor: "#FF69A550",
                hoverBackgroundColor: "#FF69A5",
                label: "Sales Out-Cash",
              },
              {
                data: data3,
                backgroundColor: "#FA8B0C50",
                hoverBackgroundColor: "#FA8B0C",
                label: "Purchase In-Cash",
              },
              {
                data: data4,
                backgroundColor: "#20C99750",
                hoverBackgroundColor: "#20C997",
                label: "Purchase Out-Cash",
              },
            ],
          },
          options: {
            maintainAspectRatio: true,
            responsive: true,
            tooltips: {
              mode: "label",
              intersect: false,
              enabled: false,
              custom: customTooltips,
              callbacks: {
                label(t, d) {
                  const dstLabel = d.datasets[t.datasetIndex].label;
                  const {
                    yLabel
                  } = t;
                  return `<span class="chart-data">${yLabel}</span> <span class="data-label">${dstLabel}</span>`;
                },
                labelColor(tooltipItem, chart) {
                  const dataset = chart.config.data.datasets[tooltipItem.datasetIndex];
                  return {
                    backgroundColor: dataset.hoverBackgroundColor,
                    borderColor: "transparent",
                    usePointStyle: true,
                  };
                },
              },
            },
            legend: {
              display: false,
              position: "bottom",
              align: "start",
              labels: {
                boxWidth: 6,
                display: true,
                usePointStyle: true,
              },
            },
            layout: {
              padding: {
                left: "0",
                right: 0,
                top: 0,
                bottom: "0",
              },
            },
            scales: {
              yAxes: [{
                gridLines: {
                  color: "#e5e9f2",
                  borderDash: [3, 3],
                  zeroLineColor: "#e5e9f2",
                  zeroLineWidth: 1,
                  zeroLineBorderDash: [3, 3],
                },
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                  max: maxrangebln,
                  stepSize: rangebln,
                  callback(value, index, values) {
                    return `${value}jt`;
                  },
                },
              }, ],
              xAxes: [{
                gridLines: {
                  display: false,
                },
                barPercentage: 0.6,
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                },
              }, ],
            },
          },
        });
      }
    }

    $('#incExp-month-tab').on("shown.bs.tab", function () {
        // filtertgl.style.display = 'none';
        var totalhari = <?= $totalhari ?>;
        // console.log(blnhutanglunas, blnhutang, blnpiutanglunas, blnpiutang);
        chartjsBarChartInExMonth(
            "barChartInEx_My",
            (data = blnhutanglunas),
            (data = blnhutang),
            (data = blnpiutanglunas),
            (data = blnpiutang),
            labels = ["1-5", "6-10", "11-15", "16-20", "21-25", "26-" + totalhari]
        );
        $('#incExp-month-tab').off();
    });

    const thnhutanglunas = <?= $thnhutanglunas ?>;
    const thnhutang = <?= $thnhutang ?>;
    const thnpiutanglunas = <?= $thnpiutanglunas ?>;
    const thnpiutang = <?= $thnpiutang ?>;

    const thl = thnhutanglunas.max();
    const th = thnhutang.max();
    const tpl = thnpiutanglunas.max();
    const tp = thnpiutang.max();
    let nilaithn = [thl, th, tpl, tp];
    const nilaithnmax = nilaithn.max();
    var maxrangethn = (nilaithnmax > 0) ? nilaithnmax : 80;
    var rangethn = (nilaithnmax > 0) ? (nilaithnmax/5).toFixed(2) : 20;

    function chartjsBarChartInExYear(selector, data1, data2, data3, data4, labels, label = "Bar chart Income Expense") {
      var ctx = document.getElementById(selector);
      if (ctx) {
        ctx.getContext("2d");
        ctx.height = window.innerWidth <= 575 ? 180 : 84;
        var chart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
                data: data1,
                backgroundColor: "#5F63F250",
                hoverBackgroundColor: "#5F63F2",
                label: "Sales In-Cash",
              },
              {
                data: data2,
                backgroundColor: "#FF69A550",
                hoverBackgroundColor: "#FF69A5",
                label: "Sales Out-Cash",
              },
              {
                data: data3,
                backgroundColor: "#FA8B0C50",
                hoverBackgroundColor: "#FA8B0C",
                label: "Purchase In-Cash",
              },
              {
                data: data4,
                backgroundColor: "#20C99750",
                hoverBackgroundColor: "#20C997",
                label: "Purchase Out-Cash",
              },
            ],
          },
          options: {
            maintainAspectRatio: true,
            responsive: true,
            tooltips: {
              mode: "label",
              intersect: false,
              enabled: false,
              custom: customTooltips,
              callbacks: {
                label(t, d) {
                  const dstLabel = d.datasets[t.datasetIndex].label;
                  const {
                    yLabel
                  } = t;
                  return `<span class="chart-data">${yLabel}</span> <span class="data-label">${dstLabel}</span>`;
                },
                labelColor(tooltipItem, chart) {
                  const dataset = chart.config.data.datasets[tooltipItem.datasetIndex];
                  return {
                    backgroundColor: dataset.hoverBackgroundColor,
                    borderColor: "transparent",
                    usePointStyle: true,
                  };
                },
              },
            },
            legend: {
              display: false,
              position: "bottom",
              align: "start",
              labels: {
                boxWidth: 6,
                display: true,
                usePointStyle: true,
              },
            },
            layout: {
              padding: {
                left: "0",
                right: 0,
                top: 0,
                bottom: "0",
              },
            },
            scales: {
              yAxes: [{
                gridLines: {
                  color: "#e5e9f2",
                  borderDash: [3, 3],
                  zeroLineColor: "#e5e9f2",
                  zeroLineWidth: 1,
                  zeroLineBorderDash: [3, 3],
                },
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                  max: maxrangethn,
                  stepSize: rangethn,
                  callback(value, index, values) {
                    return `${value}jt`;
                  },
                },
              }, ],
              xAxes: [{
                gridLines: {
                  display: false,
                },
                barPercentage: 0.6,
                ticks: {
                  beginAtZero: true,
                  fontSize: 12,
                  fontColor: "#182b49",
                },
              }, ],
            },
          },
        });
      }
    }

    $('#incExp-year-tab').on("shown.bs.tab", function() {
        // filtertgl.style.display = 'none';
        // console.log(thnhutanglunas, thnhutang, thnpiutanglunas, thnpiutang);
        chartjsBarChartInExYear(
          "barChartInEx_Y",
          (data = thnhutanglunas),
          (data = thnhutang),
          (data = thnpiutanglunas),
          (data = thnpiutang),
          labels = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ]
        );
        $('#incExp-year-tab').off();
    });

    /* Pie chart */
    const dataktg = <?= $dataktg ?>;
    var namaktg = [];
    var beratktg = [];
    var warnaktg = [];
    dataktg.forEach(element => {
      namaktg.push(element.NamaKategori);
      beratktg.push(element.BeratTotal);
      warnaktg.push(element.WarnaKategori);
    });
    // console.log(namaktg, beratktg, warnaktg);
    function chartjsPieChartOne(selector) {
      var ctx = document.getElementById(selector);
      if (ctx) {
        ctx.getContext("2d");
        var chart = new Chart(ctx, {
          type: "pie",
          data: {
            labels: namaktg,
            datasets: [{
              data: beratktg,
              backgroundColor: warnaktg,
              lbl: namaktg,
            }, ],
          },
          options: {
            maintainAspectRatio: true,
            responsive: true,
            tooltips: {
              enabled: true,
              mode: 'single',
              callbacks: {
                label: function(tooltipItems, data) {
                  return data.labels[tooltipItems.index] + ': ' + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.datasets[0].data[tooltipItems.index]).replace("Rp", "").trim() + 'kg/pcs';
                }
              }
            },
            legend: {
              display: true,
              position: "top",
              labels: {
                boxWidth: 6,
                display: true,
                usePointStyle: true,
              },
            },
            animation: {
              animateScale: true,
              animateRotate: true,
            },
          },
        });
      }
    }
    chartjsPieChartOne("mychart88");

    function getlinkbarang(){
      window.location.href = "<?= base_url('master/barang') ?>";
    }

    function getlinkpembelian(){
      window.location.href = "<?= base_url('transaksi/trans_beli') ?>";
    }

    function getlinkpenjualan(){
      window.location.href = "<?= base_url('transaksi/trans_jual') ?>";
    }

    function getlinkspk(){
      window.location.href = "<?= base_url('transaksi/spk') ?>";
    }

    const $table_data = $('#userDatatable').DataTable({
        "processing": true,
        "bAutoWidth": false,
        "pageLength": 5,
        "searching": false,
        "sDom": 'frtip',
    });
</script>