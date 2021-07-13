<?php
	class ProfileDataBase
	{
		protected function initialize(&$v)
		{
			$v = array
				(
					'adjustment' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Adjustment Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'bill' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Bill Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'billing' => array
						(
							'minimum' => array
								(
									ProfileData::DESC => 'Amount a customer bill must be, in order to be included in mail merge download by default.',
									ProfileData::TYPE => CFG_FLOAT,
									ProfileData::IS_GLOBAL => true,
								),
							'note' => array
								(
									ProfileData::DESC => 'Global billing note',
									ProfileData::TYPE => CFG_STRING,
									ProfileData::IS_GLOBAL => true,
								),
							'period' => array
								(
									ProfileData::DESC => 'Current billing period',
									ProfileData::TYPE => CFG_PERIOD,
									ProfileData::IS_GLOBAL => true,
									ProfileData::IS_REQUIRED => true,
								),
							'status' => array
								(
									ProfileData::DESC => 'Current status of customer billing',
									ProfileData::TYPE => CFG_ENUM,
									ProfileData::IS_GLOBAL => true,
									ProfileData::ENUM => array
										(
											0 => 'Scheduled',
											1 => 'Running',
											2 => 'Generated, not combined',
											3 => 'Combined',
											4 => 'Complete',
										),
								),
						),
					'client' => array
						(
							'address' => array
								(
									'1' => array
										(
											ProfileData::DESC => 'Street address on printed bills',
											ProfileData::TYPE => CFG_STRING,
											ProfileData::IS_GLOBAL => true,
											ProfileData::IS_REQUIRED => true,
										),
									'2' => array
										(
											ProfileData::DESC => 'City, State, Zip on printed bills',
											ProfileData::TYPE => CFG_STRING,
											ProfileData::IS_GLOBAL => true,
											ProfileData::IS_REQUIRED => true,
										),
								),
							'name' => array
								(
									ProfileData::DESC => 'Name on printed bills',
									ProfileData::TYPE => CFG_STRING,
									ProfileData::IS_GLOBAL => true,
									ProfileData::IS_REQUIRED => true,
								),
							'telephone' => array
								(
									ProfileData::DESC => 'Telephone on printed bills',
									ProfileData::TYPE => CFG_TELEPHONE,
									ProfileData::IS_GLOBAL => true,
								),
						),
					'complaint' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Complaint Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'configuration' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Configuration Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Configuration Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Configuration Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Configuration Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'customer' => array
						(
							'adjustment' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Width of Add Adjustment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Height of Add Adjustment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'billing' => array
								(
									'type' => array
										(
											ProfileData::DESC => 'To credit customers set amounts for each paper, set to \'old\'.  To auto calculate credits based on rates and days in period, set to \'auto\'.',
											ProfileData::TYPE => CFG_STRING,
											ProfileData::IS_GLOBAL => true,
											ProfileData::IS_REQUIRED => true,
										),
								),
							'complaint' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Width of Add Complaint Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Height of Add Complaint Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Customer Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Customer Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'payment' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Customer Add Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Customer Add Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
									'edit' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Customer Edit Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Customer Edit Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
									'view' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Customer View Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Customer View Payment Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'rates' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Add Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Add Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
									'edit' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Edit Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Edit Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
									'view' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of View Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of View Customer Rate Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'service' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Width of Add Stop/Start Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Height of Add Stop/Start Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'type' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Width of Add Type Change Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Height of Add Type Change Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Customer Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Customer Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'customers' => array
						(
							'combined' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Add Combined Customer Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Add Combined Customer Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
									'edit' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Height of Edit Combined Customer Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Width of Edit Combined Customer Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
							'daily' => array
								(
									'only' => array
										(
											'cost' => array
												(
													ProfileData::DESC => 'Rate client pays for daily only papers for 1 period (home delivery rate)',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
													ProfileData::IS_REQUIRED => true,
												),
										),
									'single' => array
										(
											'cost' => array
												(
													ProfileData::DESC => 'Rate client pays for single daily paper (wholesale rate)',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
													ProfileData::IS_REQUIRED => true,
												),
										),
								),
							'default' => array
								(
									'billing' => array
										(
											'state' => array
												(
													ProfileData::DESC => 'This state will be pre-selected in the billing address state field when first loading the Customer New page.',
													ProfileData::TYPE => CFG_STRING,
													ProfileData::IS_GLOBAL => true,
												),
										),
									'delivery' => array
										(
											'state' => array
												(
													ProfileData::DESC => 'This state will be pre-selected in the delivery address state field when first loading the Customer New page.',
													ProfileData::TYPE => CFG_STRING,
													ProfileData::IS_GLOBAL => true,
												),
										),
								),
							'sunday' => array
								(
									'only' => array
										(
											'cost' => array
												(
													ProfileData::DESC => 'Rate client pays for sunday only papers for 1 period (home delivery rate)',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
													ProfileData::IS_REQUIRED => true,
												),
										),
									'single' => array
										(
											'cost' => array
												(
													ProfileData::DESC => 'Rate client pays for single sunday paper (wholesale rate)',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
													ProfileData::IS_REQUIRED => true,
												),
										),
								),
						),
					'default' => array
						(
							'customer' => array
								(
									'lookup' => array
										(
											'limit' => array
												(
													ProfileData::DESC => 'Initial value for limit on Customers / Lookup page.',
													ProfileData::TYPE => CFG_INTEGER,
													ProfileData::IS_GLOBAL => true,
												),
										),
									'route' => array
										(
											ProfileData::DESC => 'Route to pre-select when a route is requested.',
											ProfileData::TYPE => CFG_ROUTE,
											ProfileData::IS_GLOBAL => true,
										),
								),
							'table' => array
								(
									'background' => array
										(
											'even' => array
												(
													ProfileData::DESC => 'Background color fo even rows of tables.',
													ProfileData::TYPE => CFG_COLOR,
												),
											'odd' => array
												(
													ProfileData::DESC => 'Background color for odd rows of tables.',
													ProfileData::TYPE => CFG_COLOR,
												),
										),
									'border' => array
										(
											ProfileData::DESC => 'Whether to put a border on tables by default.',
											ProfileData::TYPE => CFG_BOOLEAN,
											'color' => array
												(
													ProfileData::DESC => 'Color to use as border color by default.',
													ProfileData::TYPE => CFG_COLOR,
												),
										),
								),
							'title' => array
								(
									ProfileData::DESC => 'Identifying title on all pages',
									ProfileData::TYPE => CFG_STRING,
									ProfileData::IS_GLOBAL => true,
								),
						),
					'flag' => array
						(
							'stop' => array
								(
									'billing' => array
										(
											'minimum' => array
												(
													ProfileData::DESC => 'Flag Stop customers need to owe at least this amount before a bill is printed',
													ProfileData::TYPE => CFG_FLOAT,
													ProfileData::IS_GLOBAL => true,
												),
										),
									'daily' => array
										(
											'rate' => array
												(
													ProfileData::DESC => 'Amount to charge a Flag Stop customer for a single daily paper',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
												),
										),
									'sunday' => array
										(
											'rate' => array
												(
													ProfileData::DESC => 'Amount to charge a Flag Stop customer for a single sunday paper',
													ProfileData::TYPE => CFG_MONEY,
													ProfileData::IS_GLOBAL => true,
												),
										),
									'type' => array
										(
											ProfileData::DESC => 'Flag Stop Type ID',
											ProfileData::TYPE => CFG_TYPE,
											ProfileData::IS_GLOBAL => true,
										),
								),
						),
					'flagstop' => array
						(
							'service' => array
								(
									'add' => array
										(
											'popup' => array
												(
													'height' => array
														(
															ProfileData::DESC => 'Width of Add Start/Stop Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
													'width' => array
														(
															ProfileData::DESC => 'Height of Add Start/Stop Popup',
															ProfileData::TYPE => CFG_INTEGER,
														),
												),
										),
								),
						),
					'group' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Group Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Group Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Group Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Group Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
                    'maintenance' => array
                        (
                            'active' => array
                                (
                                    ProfileData::DESC => 'When True, only Russ is allowed access to anything.  Everybody else will be forwarded to maintenance message page.',
                                    ProfileData::TYPE => CFG_BOOLEAN,
                                    ProfileData::IS_GLOBAL => true,
                                ),
                        ),
					'period' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Period Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'profile' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Profile Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Profile Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Profile Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Profile Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
                    'returns' => array
                        (
                            'receiver' => array
                                (
                                    ProfileData::DESC => 'Email addres to send Store and Rack return reports.',
                                    ProfileData::TYPE => CFG_STRING,
                                    ProfileData::IS_GLOBAL => true,
                                ),
                        ),
					'route' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Route Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Route Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Route Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Route Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'service' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Stop/Start Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'servicetype' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'view' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of View Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of View Type Change Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
					'user' => array
						(
							'add' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Add User Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Add User Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
							'edit' => array
								(
									'popup' => array
										(
											'height' => array
												(
													ProfileData::DESC => 'Height of Edit User Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
											'width' => array
												(
													ProfileData::DESC => 'Width of Edit User Popup',
													ProfileData::TYPE => CFG_INTEGER,
												),
										),
								),
						),
				);
		}
	
		protected function _keys(&$v, $global)
		{
			if ($global)
			{
				$v = array
					(
						'billing-minimum',
						'billing-note',
						'billing-period',
						'billing-status',
						'client-address-1',
						'client-address-2',
						'client-name',
						'client-telephone',
						'customer-billing-type',
						'customers-daily-only-cost',
						'customers-daily-single-cost',
						'customers-default-billing-state',
						'customers-default-delivery-state',
						'customers-sunday-only-cost',
						'customers-sunday-single-cost',
						'default-customer-lookup-limit',
						'default-customer-route',
						'default-title',
						'flag-stop-billing-minimum',
						'flag-stop-daily-rate',
						'flag-stop-sunday-rate',
						'flag-stop-type',
                        'maintenance-active',
                        'returns-receiver',
					);
			}
			else
			{
				$v = array
					(
						'adjustment-add-popup-height',
						'adjustment-add-popup-width',
						'adjustment-edit-popup-height',
						'adjustment-edit-popup-width',
						'adjustment-view-popup-height',
						'adjustment-view-popup-width',
						'bill-add-popup-height',
						'bill-add-popup-width',
						'bill-edit-popup-height',
						'bill-edit-popup-width',
						'bill-view-popup-height',
						'bill-view-popup-width',
						'complaint-add-popup-height',
						'complaint-add-popup-width',
						'complaint-edit-popup-height',
						'complaint-edit-popup-width',
						'complaint-view-popup-height',
						'complaint-view-popup-width',
						'configuration-add-popup-height',
						'configuration-add-popup-width',
						'configuration-edit-popup-height',
						'configuration-edit-popup-width',
						'customer-adjustment-add-popup-height',
						'customer-adjustment-add-popup-width',
						'customer-complaint-add-popup-height',
						'customer-complaint-add-popup-width',
						'customer-edit-popup-height',
						'customer-edit-popup-width',
						'customer-payment-add-popup-height',
						'customer-payment-add-popup-width',
						'customer-payment-edit-popup-height',
						'customer-payment-edit-popup-width',
						'customer-payment-view-popup-height',
						'customer-payment-view-popup-width',
						'customer-rates-add-popup-height',
						'customer-rates-add-popup-width',
						'customer-rates-edit-popup-height',
						'customer-rates-edit-popup-width',
						'customer-rates-view-popup-height',
						'customer-rates-view-popup-width',
						'customer-service-add-popup-height',
						'customer-service-add-popup-width',
						'customer-type-add-popup-height',
						'customer-type-add-popup-width',
						'customer-view-popup-height',
						'customer-view-popup-width',
						'customers-combined-add-popup-height',
						'customers-combined-add-popup-width',
						'customers-combined-edit-popup-height',
						'customers-combined-edit-popup-width',
						'default-table-background-even',
						'default-table-background-odd',
						'default-table-border',
						'default-table-border-color',
						'flagstop-service-add-popup-height',
						'flagstop-service-add-popup-width',
						'group-add-popup-height',
						'group-add-popup-width',
						'group-edit-popup-height',
						'group-edit-popup-width',
						'period-add-popup-height',
						'period-add-popup-width',
						'period-edit-popup-height',
						'period-edit-popup-width',
						'period-view-popup-height',
						'period-view-popup-width',
						'profile-add-popup-height',
						'profile-add-popup-width',
						'profile-edit-popup-height',
						'profile-edit-popup-width',
						'route-add-popup-height',
						'route-add-popup-width',
						'route-edit-popup-height',
						'route-edit-popup-width',
						'service-add-popup-height',
						'service-add-popup-width',
						'service-edit-popup-height',
						'service-edit-popup-width',
						'service-view-popup-height',
						'service-view-popup-width',
						'servicetype-add-popup-height',
						'servicetype-add-popup-width',
						'servicetype-edit-popup-height',
						'servicetype-edit-popup-width',
						'servicetype-view-popup-height',
						'servicetype-view-popup-width',
						'user-add-popup-height',
						'user-add-popup-width',
						'user-edit-popup-height',
						'user-edit-popup-width',
					);
			}
		}
	
		protected function _keys1(&$v, $global)
		{
			if ($global)
			{
				$v = array
					(
						'billing',
						'client',
						'customer',
						'customers',
						'default',
						'flag',
                        'maintenance',
                        'returns',
					);
			}
			else
			{
				$v = array
					(
						'adjustment',
						'bill',
						'complaint',
						'configuration',
						'customer',
						'customers',
						'default',
						'flagstop',
						'group',
						'period',
						'profile',
						'route',
						'service',
						'servicetype',
						'user',
					);
			}
		}
	
		protected function _keys2(&$v, $global)
		{
			if ($global)
			{
				$v = array
                    (
                        'active',
						'address',
						'billing',
						'customer',
						'daily',
						'default',
						'minimum',
						'name',
						'note',
                        'period',
                        'receiver',
						'status',
						'stop',
						'sunday',
						'telephone',
						'title',
					);
			}
			else
			{
				$v = array
					(
						'add',
						'adjustment',
						'combined',
						'complaint',
						'edit',
						'payment',
						'rates',
						'service',
						'table',
						'type',
						'view',
					);
			}
		}
	
		protected function _keys3(&$v, $global)
		{
			if ($global)
			{
				$v = array
					(
						'1',
						'2',
						'billing',
						'daily',
						'delivery',
						'lookup',
						'only',
						'route',
						'single',
						'sunday',
						'type',
					);
			}
			else
			{
				$v = array
					(
						'add',
						'background',
						'border',
						'edit',
						'popup',
						'view',
					);
			}
		}
	
		protected function _keys2FromKeys1(&$v, $global)
		{
			if ($global)
			{
				$v = array
					(
						'billing' => array
							(
								'minimum',
								'note',
								'period',
								'status',
							),
						'client' => array
							(
								'address',
								'name',
								'telephone',
							),
						'customer' => array
							(
								'billing',
							),
						'customers' => array
							(
								'daily',
								'default',
								'sunday',
							),
						'default' => array
							(
								'customer',
								'title',
							),
						'flag' => array
							(
								'stop',
                            ),
                        'maintenance' => array
                            (
                                'active',
                            ),
                        'returns' => array
                            (
                                'receiver',
                            ),
					);
			}
			else
			{
				$v = array
					(
						'adjustment' => array
							(
								'add',
								'edit',
								'view',
							),
						'bill' => array
							(
								'add',
								'edit',
								'view',
							),
						'complaint' => array
							(
								'add',
								'edit',
								'view',
							),
						'configuration' => array
							(
								'add',
								'edit',
							),
						'customer' => array
							(
								'adjustment',
								'complaint',
								'edit',
								'payment',
								'rates',
								'service',
								'type',
								'view',
							),
						'customers' => array
							(
								'combined',
							),
						'default' => array
							(
								'table',
							),
						'flagstop' => array
							(
								'service',
							),
						'group' => array
							(
								'add',
								'edit',
							),
						'period' => array
							(
								'add',
								'edit',
								'view',
							),
						'profile' => array
							(
								'add',
								'edit',
							),
						'route' => array
							(
								'add',
								'edit',
							),
						'service' => array
							(
								'add',
								'edit',
								'view',
							),
						'servicetype' => array
							(
								'add',
								'edit',
								'view',
							),
						'user' => array
							(
								'add',
								'edit',
							),
					);
			}
		}
	
	}
?>
