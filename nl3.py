
from nl import app, db
from nl.models import *

@app.shell_context_processor
def make_shell_context():
    return {'db': db,
            'Alert': Alert,
            'AuditLog': AuditLog,
            'Configuration': Configuration,
            'Customer': Customer,
            'CustomerAddresses': CustomerAddresses,
            'CustomerAdjustments': CustomerAdjustments,
            'CustomerBills': CustomerBills,
            'CustomerBillsLog': CustomerBillsLog,
            'CustomerCombinedBills': CustomerCombinedBills,
            'CustomerComplaints': CustomerComplaints,
            'CustomerNames': CustomerNames,
            'CustomerPayments': CustomerPayments,
            'CustomerRates': CustomerRates,
            'CustomerServices': CustomerServices,
            'CustomerServiceTypes': CustomerServiceTypes,
            'CustomerTelephones': CustomerTelephones,
            'CustomerTypes': CustomerTypes,
            'Error': Error,
            'Group': Group,
            'GroupConfigurations': GroupConfigurations,
            'Period': Period,
            'Route': Route,
            'RouteChangeNotes': RouteChangeNotes,
            'RouteSequences': RouteSequences,
            'User': User,
            'UserConfigurations': UserConfigurations
            }


