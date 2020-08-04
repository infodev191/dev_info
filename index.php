<!DOCTYPE html>
<html>
    <head>
        <title> Analytics</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <style>
            .secion2 table  {
                border-collapse: collapse;
                width: 100%;
                color: #343A40;
                font-family: 'Hind Siliguri', sans-serif;
                line-height: 2.6;
                font-size: 12px;
                text-align: left;
                text-indent: 10px;
            }
            th {
                background-color: #407AAD;
                color: white;
            }

            a {
                color: white;
                text-decoration: none;
            }

            tr:nth-child(even) {background-color: #f2f2f2}
            section.inner_section {
			    margin: 15px 0;
			}
        </style>
    </head>
    <body>
		<div class="container">
	    	<section class="inner_section secion1">	        
	            <div class="row">
	                <div class="col-md-12">
	                    <form class="form-inline"  method="get" autocomplete="off">
	                        <div class="form-group">                           
	                            <input type="text" class="form-control" id="dt1" placeholder="From Date" name="from_date" autocomplete="off" value="<?php if(isset($_REQUEST['from_date'])) {echo $_REQUEST['from_date']; } ?>" readonly>
	                        </div>
	                        <div class="form-group">                            
	                            <input type="text" class="form-control" id="dt2" placeholder="To Date" name="to_date" autocomplete="off" value="<?php if(isset($_REQUEST['to_date'])) {echo $_REQUEST['to_date']; } ?>" readonly>
	                        </div>
	                        <div class="form-group">
	                        	<?php if(isset($_REQUEST['type'])) { ?>
	                        		<?php $selected_ddl=$_REQUEST['type']; ?>
	                            <select class="form-control" name="type">
	                                <option value="up_date" <?php if($selected_ddl=="up_date") {echo "selected"; } ?>>UP Date</option>
	                                <option value="add_date" <?php if($selected_ddl=="add_date") {echo "selected"; } ?>>Add Date</option>
	                            </select>
	                        	<?php } else { ?>
                        		<select class="form-control" name="type">
	                                <option value="up_date">UP Date</option>
	                                <option value="add_date">Add Date</option>
	                            </select>
	                        	<?php } ?>
	                        </div>
	                        <button type="submit" id="from_id" class="btn btn-sm btn-warning">Submit</button>
	                    </form>
	                </div>
	            </div>		        
		    </section>

		    <section class="inner_section secion2">
	            <div class="row">
	                <div class="col-md-12">

	                	<?php
	                        $conn = mysqli_connect("localhost", "root", "", "demo_test");
	                        // Check connection
	                        if ($conn->connect_error) {
	                            die("Connection failed: " . $conn->connect_error);
	                        }
	                        @$formdate1 = strtotime($_REQUEST['from_date']);
                             $formdate = date('Y-m-d', $formdate1);

                             @$todate1 = strtotime($_REQUEST['to_date']);
                             $todate = date('Y-m-d', $todate1);

                            @$type =  $_REQUEST['type'];

                            $sql = $trigger_price = $appt_date = $updown = "";

                            $sql .="SELECT  c.*, s.* from clpost c JOIN status_board s on c.id=s.clpost_id";

                            $updown .="SELECT  c.bad, count(*) as count from clpost c JOIN status_board s on c.id=s.clpost_id";


                            $triggerprc_or_appt_date="SELECT sum(case when s.trigger_price > 0 then 1 else 0 end ) AS notzero, sum(case when appt_date > 0 then 1 else 0 end ) AS aptdate, sum(case when s.sold = 'yes' then 1 else 0 end ) AS sold_count from clpost c JOIN status_board s on c.id=s.clpost_id";
                            
                            if($type == 'up_date'){                              	
                              	$add_query =" where c.up_date BETWEEN '".$formdate."' AND '".$todate."'";
                              	$sql .= $add_query;
                              	$updown .=  $add_query." group BY c.bad";                              	
                              	$triggerprc_or_appt_date .=$add_query;

                            } else if($type == 'add_date'){                              	
                              	$add_query =" where s.add_date BETWEEN '".$formdate."' AND '".$todate."'";
                              	$sql .= $add_query;
                              	$updown .=  $add_query." group BY c.bad";
                              	$triggerprc_or_appt_date .=$add_query;
                            } 



                            @$result = $conn->query($sql);
	                        if (@$result->num_rows > 0) {
	                    ?>

	                    <table class="table table-hover">
	                        <tr>
	                            <th><a href="#" title="This is the Name of the Car Dealership, it appears in both the clPost and status_board tables in the column dealer_name"> Dealer Name</a></th>
	                            <th><a href="#" title="This is the location of the dealership from the store column in the clPost table, it also appears in the status_board table in the column named loc, but the two values do not always match"> Store</a></th>
	                            <th><a href="#" title="This is the total number of rows from clPost that are added on a given date from the clPost table that are associated with a specific Dealership"> Fresh</a></th>
	                            <th><a href="#" title="This is the total number of vehicles from status_board for a specific Dealer where the bad column is marked no"> Up</a></th>
	                            <th><a href="#" title="This is the total number of rows in the status_board table for vehicles where the column bad=yes for a specific Dealership for a specific date based on the date in the column up_date"> Down</a></th>
	                            <th><a href="#" title="This is the total number of rows from the status_board table where there is text in the vetted column, but the sold column says no"> Ready to Buy</a></th>
	                            <th><a href="#" title="This is the total number of rows from the status board table that have the appointment column not blank for a specific date that belong to a certain Dealership"> Appointments</a></th>
	                            <th><a href="#" title="This is the total number of rows from the status_board table for a Dealership that has cars marked yes in the column named sold"> Purchased</a></th>
	                        </tr>


	                        <?php

	                        	 $data = array();
	                        	 foreach($conn->query($updown) as $row) {
	                        	 	$data[] = $row;
	                        	 }
	                        	 
	                        	 $trg_appt = "";
	                        	foreach($conn->query($triggerprc_or_appt_date) as $nonzero) {
	                        	 	$trg_appt = array(
	                        	 		'notzero'=> $nonzero['notzero'],
	                        	 		'aptdate'=> $nonzero['aptdate'],
	                        	 		'sold_count' => $nonzero['sold_count']
	                        	 	);
	                        	 }

	                        	$aptdate = "";
	                        	  	foreach($conn->query("SELECT count(*) as aptdate  FROM `status_board` WHERE `appt_date` != ''") as $date) {
	                        	 	$aptdate = $date;
	                        	}

	                        	 $pdata = array();
	                        	 foreach($conn->query('SELECT sold, COUNT(*) as count FROM status_board GROUP BY sold ORDER BY count DESC') as $prow) {
	                        	 	$pdata[] = $prow;
	                        	 }
	                        	
	                  
	                            while ($row = $result->fetch_assoc()) {
	                                echo "<tr><td>" .
	                                $row["dealer_name"] . "</td><td>" .
	                                $row["store"] . "</td><td>" .
	                                $result->num_rows . "</td><td>" .
	                                $data[1]['count'] . "</td><td>" .
	                                $data[0]['count'] . "</td><td>" .
	                                $trg_appt['notzero'] . "</td><td>" .
	                                $trg_appt['aptdate'] . "</td><td>" .
	                                $trg_appt['sold_count'] . "</td><tr/>";
	                             	                                
	                            }
	                            echo "</table>";
	                        } else {
	                           echo "<p class='text-danger'>No Result Found...</p>";
	                        }
	                        $conn->close();
	                        ?>	                    
	                </div>
	            </div>            
	        </section>
	    </div>

	    <script>
	    	$(document).ready(function () {
	    		/*$('#from_id').click(function(){
	    				var form_date = $('#dt1').val();
	    				var to_date   = $('#dt2').val();
    					alert(to_date);
					});	*/

			    $("#dt1").datepicker({
			    	startDate: '01/01/2000',
			        dateFormat: "M-dd-yy",
			        //minDate: 0,
			        onSelect: function () {
			            var dt2 = $('#dt2');
			            var startDate = $(this).datepicker('getDate');			            
			            //add 30 days to selected date
			            startDate.setDate(startDate.getDate() + 30);
			            var minDate = $(this).datepicker('getDate');
			            var dt2Date = dt2.datepicker('getDate');
			            //difference in days. 86400 seconds in day, 1000 ms in second
			            var dateDiff = (dt2Date - minDate)/(86400 * 1000);
                         
			            //dt2 not set or dt1 date is greater than dt2 date
			            if (dt2Date == null || dateDiff < 0) {
			                    //dt2.datepicker('setDate', minDate);
			            }
			            else if (dateDiff > 30){
			                    //dt2.datepicker('setDate', startDate);
			            }			            
			        }
			    });
			    $('#dt2').datepicker({
			        dateFormat: "M-dd-yy",
			        //minDate: 0
			    });
			});
	    </script>
    </body>
</html>










