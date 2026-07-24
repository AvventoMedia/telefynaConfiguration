<form ng-submit="addMailerPassword()">
	<div class="card p-3 mb-3 border-0">
		<div class="form-check form-switch">
			<input class="form-check-input" ng-model="config.alerts.enabled" type="checkbox" id="chkAlerts">
			<label class="form-check-label font-weight-bold" for="chkAlerts">Enable Automated Email Alerts</label>
		</div>
	</div>
	
	<div class="card p-3 mb-3 border-0">
		<h6 class="font-weight-bold mb-3"><i class="fa-solid fa-paper-plane me-2 text-primary"></i>SMTP Sender Configuration</h6>
		<div class="row">
			<div class="col-md-6 mb-3">
				<label class="form-label">SMTP Host</label>
				<input class="form-control" required ng-model="config.alerts.mailer.host" placeholder="smtp.gmail.com" type="text" ng-change="modifying()">
			</div>
			<div class="col-md-6 mb-3">
				<label class="form-label">SMTP Port</label>
				<input class="form-control" required ng-model="config.alerts.mailer.port" placeholder="587" type="number" ng-change="modifying()">
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3">
				<label class="form-label">Sender Email Address</label>
				<input class="form-control" required ng-model="config.alerts.mailer.email" placeholder="apps@avventohome.org" type="text" ng-change="modifying()">
			</div>
			<div class="col-md-6 mb-3">
				<label class="form-label">Sender Email Password</label>
				<input class="form-control" id="pswd" required type="password">
			</div>
		</div>
	</div>
	
	<div class="card p-3 border-0 table-responsive mb-4">
		<h6 class="font-weight-bold mb-3"><i class="fa-solid fa-users me-2 text-primary"></i>Alert Receivers</h6>
		<table class="table table-striped align-middle">
			<thead>
				<tr>
					<th scope="col">Select</th>
					<th scope="col">Role</th>
					<th scope="col">Attach Config</th>
					<th scope="col">Log Days</th>
					<th scope="col">Receiver Email(s)</th>
				</tr>
			</thead>
			<tbody>
				<!-- Add Receiver Row -->
				<tr class="table-primary">
					<td></td>
					<td>
						<select class="form-select form-select-sm" ng-model="alert.eventCategory">
							<option value="ADMIN">Administrator</option>
							<option value="BROADCAST">Broadcaster</option>
						</select>
					</td>
					<td class="text-center">
						<input class="form-check-input" ng-model="alert.attachConfig" type="checkbox" ng-disabled="isEmpty(alert.eventCategory) || alert.eventCategory != 'ADMIN'">
					</td>
					<td>
						<input class="form-control form-control-sm" ng-model="alert.attachAuditLog" type="number" ng-disabled="isEmpty(alert.eventCategory) || alert.eventCategory != 'ADMIN'">
					</td>
					<td>
						<input class="form-control form-control-sm" ng-model="alert.emails" placeholder="email1@domain.com, email2@domain.com" type="text">
					</td>
				</tr>
				<!-- Display Receivers -->
				<tr ng-repeat="(k, s) in config.alerts.subscribers">
					<td>
						<input class="receiver form-check-input" value="{{k}}" type="checkbox">
					</td>
					<td><span class="badge bg-secondary">{{s.eventCategory}}</span></td>
					<td class="text-center">
						<i class="fa-solid" ng-class="s.attachConfig ? 'fa-check text-success' : 'fa-xmark text-danger'"></i>
					</td>
					<td>{{s.attachAuditLog}}</td>
					<td><span class="font-monospace small">{{s.emails}}</span></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="d-flex justify-content-between border-top pt-4 mt-2">
		<button class="btn btn-outline-danger" type="button" ng-disabled="isEmpty(config.alerts.subscribers)" ng-click="deleteReceivers()">
			<i class="fa-solid fa-trash me-1"></i> Delete Selected
		</button>
		<div class="d-flex gap-2">
			<button class="btn btn-primary" type="button" ng-disabled="isEmpty(alert.emails) || isEmpty(alert.attachConfig) || isEmpty(alert.attachAuditLog) || isEmpty(alert.eventCategory) || invalidSubScriber()" ng-click="addAlert()">
				<i class="fa-solid fa-user-plus me-1"></i> Add Receiver
			</button>
			<button class="btn btn-success px-4" type="submit" ng-disabled="isEmpty(config.alerts.subscribers)">
				<i class="fa-solid fa-floppy-disk me-1"></i> Save Settings
			</button>
		</div>
	</div>
</form>