@extends('layouts.admin_app')

@section('content')
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Dashboard </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-md-4">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
				 <h3>RM {{ number_format($totalSales->totalSales, 2) }}</h3>
				 <p>Monthly Sales</p>
				</div>
              <div class="icon">
                	<i class="fa fa-usd"></i>
              </div>
              <a href="{{ route('transaction.transactions.index') }}"class="small-box-footer"> View all transaction <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>            
		 <!-- ./col -->
          <div class="col-md-4">
            <!-- small box -->
             <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $totalMembers->totalMembers }}</h3>

                <p>Total Member</p>
              </div>
              <div class="icon">
                <i class="fa fa-money"></i>
              </div>
             	<a href="{{ route('member.members.index') }}" class="small-box-footer">View all Member <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
           <!-- ./col -->
          <div class="col-md-4">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $totalProduct->totalProduct }}</h3>

                <p>Total Product</p>
              </div>
              <div class="icon">
                <i class="fa fa-users"></i>
              </div>
             <a href="{{ route('product.products.index') }}"class="small-box-footer">View all Product  <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->			           
        </div>
        <!-- /.row -->
        <!-- Main row -->
<!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-sm-12">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">
                  <i class="ace-icon fa fa-signal"></i>&nbsp;Sale Stats
                </h4>
                <div class="card-tools">
                  <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                      <a class="nav-link active" href="#daily-tab" data-toggle="tab">Daily</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#monthly-tab" data-toggle="tab">Monthly</a>
                    </li>
                    &nbsp;
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              
                  </ul>
                </div>

              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                  <!-- Morris chart - Sales -->
                  <div id="daily-tab" class="tab-pane active">
                       
                      <canvas id="line-chart-daily" width="800" height="450"></canvas>
                   </div>
                  <div id="monthly-tab" class="tab-pane">
                    <canvas id="line-chart-monthly" width="800" height="450"></canvas>
                  </div>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->


<form>
  <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Transaction (Waiting for approval)</h3>

           <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-bordered">
                  <thead>
                    <tbody>
                    <tr>
                      <th>#</th>
            <th>Transaction no</th>
            <th>Buyer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created</th>
            <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    </tr>
              @if(!$transactions->isEmpty())
              @foreach($transactions as $key => $transaction)
              <tr>
                <td>{{ $key+1 }}
                  <input type="hidden" name="tid" value="{{ $transaction->id }}">
                </td>
                <td>{{ $transaction->transaction_no }}</td>
                <td>{{ $transaction->user_id }}</td>
                <td>{{ $transaction->grand_total }}</td>
                <td>
                  <span class="label label-info">
                    Waiting for approval
                  </span>
                </td>
                <td>13/02/2020 15:40:35</td>
                <td align="center">
                  <a href="{{ route('transaction.transactions.edit', $transaction->id) }}" class="btn btn-primary btn-sm" title="View Transaction">
                    <i class="fa fa-search"></i>
                  </a>
                  <a href="#" class="btn btn-success btn-sm change_action" data-id="1" title="Approve This Transaction">
                    <i class="fa fa-check"></i>
                  </a>
                  <a href="#" class="btn btn-danger btn-sm change_action" data-id="96" title="Reject This Transaction">
                    <i class="fa fa-ban"></i>
                  </a>
                </td>
              </tr>
            </tbody>
              @endforeach
              @else
              <tr>
                <td colspan="7" align="center">No Result Found</td>
              </tr>
              @endif
            </table>
          </div>
        </div>
      </div>
    </form>
	</div>
</section>
@endsection

@section('js')
<script type="text/javascript">
$('.change_action').click( function(e){
	e.preventDefault();
	$('.loading-gif').show();

	var ele = $(this);
	var action_id = $(this).data('id');
	var tid = $(this).closest('tr').find('input[name="tid"]').val();
	var fd = new FormData();
	fd.append('action_id', action_id);
	fd.append('tid', tid);
  fd.append('_token', '{{ csrf_token() }}');
  
	if(action_id == '1'){
		var confirmMessage = confirm('Complete This Transaction?');
	}else if(action_id == '95'){
		var confirmMessage = confirm('Cancel This Transaction? ');
	}else if(action_id == '96'){
		var confirmMessage = confirm('Reject This Transaction?');
	}else if(action_id == '11'){
		var confirmMessage = confirm('Delivered?');
	}


	if(confirmMessage == true){
		$.ajax({
	       url: '{{ route("change_transaction_action") }}',
	       type: 'post',
	       data: fd,
	       contentType: false,
	       processData: false,
	       success: function(response){
	       		$('.loading-gif').hide();
	       		
	       		toastr.success('Update Successful');
	       		window.location.href = "{{ route('transaction.transactions.index') }}";
	       		
	       },
	    });			
	}else{
		$('.loading-gif').hide();
	}
});


$('.change_action_merchant').click( function(e){
		e.preventDefault();

		$('.loading-gif').show();
		var ele = $(this);
		var action_id = $(this).data('id');
		var mid = $(this).closest('tr').find('input[name="mid"]').val();
		var fd = new FormData();
		fd.append('action_id', action_id);
		fd.append('mid', mid);


		if(action_id == '1'){
			var action_confirm = confirm('Approve this agent?');
		}else{
			var action_confirm = confirm('Reject this agent?');
		}
		if(action_confirm == true){
			$.ajax({
		       url: '',
		       type: 'post',
		       data: fd,
		       contentType: false,
		       processData: false,
		       success: function(response){
		       		// alert(response);
		       		$('.loading-gif').hide();
		       		toastr.success('Update Successful');
		       		window.location.href = "";
		       },
		    });			
		}else{
			$('.loading-gif').hide();
		}
	});

var today = new Date(); // current date
var end = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate(); // end date of month
var month = today.getMonth()+1;
var result = [];

for(let i = 1; i <= end; i++){
   result.push(today.getFullYear() + '-' + (today.getMonth() < 10? '0'+month: month) +'-'+ (i < 10 ? '0'+ i: i))
}

var date = 
new Chart(document.getElementById("line-chart-daily"), {
  type: 'line',
  data: {
    labels: result,
    datasets: [{ 
        data: {{ json_encode($list) }},
        label: "Daily Sales",
        borderColor: "#3e95cd",
        fill: false
      }
    ]
  },
  options: {
    title: {
      display: true,
    }
  }
});

var date1 = 
new Chart(document.getElementById("line-chart-monthly"), {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun','July','Aug','Sep','Oct','Nov','Dec'],
    datasets: [{ 
        data: {{ json_encode($list2) }},
        label: "Monthly Sales",
        borderColor: "#3e95cd",
        fill: false
      }
    ]
  },
  options: {
    title: {
      display: true,
    }
  }
});


var placeholder = $('#piechart-placeholder-today').css({'width':'90%' , 'min-height':'300px'});
var data = [
{ label: "Customer",  data: '{{ $totalCustomer->totalCustomer }}', color: "#2091CF"},
]
function drawPieChart(placeholder, data, position) {
	  $.plot(placeholder, data, {
	series: {
		pie: {
			show: true,
			tilt:0.8,
			highlight: {
				opacity: 0.25
			},
			stroke: {
				color: '#fff',
				width: 2
			},
			startAngle: 2
		}
	},
	legend: {
		show: true,
		position: position || "ne", 
		labelBoxBorderColor: null,
		margin:[-30,15]
	}
	,
	grid: {
		hoverable: true,
		clickable: true
	}
 })
}
drawPieChart(placeholder, data);

/**
we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
so that's not needed actually.
*/
placeholder.data('chart', data);
placeholder.data('draw', drawPieChart);


//pie chart tooltip example
var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
var previousPoint = null;

placeholder.on('plothover', function (event, pos, item) {
if(item) {
	if (previousPoint != item.seriesIndex) {
		previousPoint = item.seriesIndex;
		var tip = item.series['label'] + " : " + item.series['percent']+'%';
		$tooltip.show().children(0).text(tip);
	}
	$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
} else {
	$tooltip.hide();
	previousPoint = null;
}

});



</script>
@endsection