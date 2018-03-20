<?php echo file_get_contents('header.html'); ?>

		<form action="calculate.php" class="form-horizontal">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="Age">Age</label> <input class="form-control input-md" id="Age" name="age" type="text" value="38" required>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="weight">Weight (LBS)</label> <input class="form-control input-md" id="weight" name="weight" type="text" value="182" required>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="height">Height</label> <br>
          <input style="width:3em; display:inline;" class="form-control input-sm" id="height-f" name="height-f" type="text" value="5"  required> ' <input style="width:4em; display:inline;" class="form-control input-sm" id="height-i" name="height-i" type="text" value="6" required> "
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="speed">Walking Speed (MPH)</label> <input class="form-control input-md" id="speed" name="speed" type="text" value="3.1"  required> <span class="help-block">Average walking speed is 3.1MPH</span>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="sex">Sex</label>
						<div class="radio">
							<label for="sex-0"><input checked="checked" id="sex-0" name="sex" type="radio" value="f"> Female</label>
						</div>
						<div class="radio">
							<label for="sex-1"><input id="sex-1" name="sex" type="radio" value="m"> Male</label>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label" for="Diet">Diet</label>
						<div class="radio">
							<label for="Diet-0"><input checked="checked" id="Diet-0" name="diet" type="radio" value="o"> Omnivore</label>
						</div>
						<div class="radio">
							<label for="Diet-1"><input id="Diet-1" name="diet" type="radio" value="v"> Vegetarian</label>
						</div>
					</div>
				</div>
			</div><!-- row -->
			<div class="form-group text-center">
				<label class="control-label" for="Submit"></label> <button class="btn btn-info" type="submit">Submit</button>
			</div>
		</form>

<?php echo file_get_contents('footer.html'); ?>
