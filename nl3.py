
from pprint import pprint

from flask import current_app
from sqlalchemy import distinct, func, select, and_, or_

from nl import create_app, db
from nl import models
from nl.models import auth, config, customers, routes


app = create_app()

@app.shell_context_processor
def make_shell_context():
    return {
        'app': current_app,
        'db': db,
        'security': current_app.security,
        # nl.models
        'AuditLog': models.AuditLog,
        'Error': models.Error,
        'Period': models.Period,
        # nl.models.auth
        #'Group': Group,
        'Role': auth.Role,
        #'Security': Security,
        'RolesUsers': auth.RolesUsers,
        'User': auth.User,
        # nl.models.config
        'Config': config.Config,
        #'GroupConfig': GroupConfig,
        'UserConfig': config.UserConfig,
        # nl.models.customers
        'Customer': customers.Customer,
        'Address': customers.Address,
        'Adjustment': customers.Adjustment,
        'Bill': customers.Bill,
        'BillLog': customers.BillLog,
        'CombinedBill': customers.CombinedBill,
        'Complaint': customers.Complaint,
        'Name': customers.Name,
        'Payment': customers.Payment,
        'Rate': customers.Rate,
        'ServiceChange': customers.ServiceChange,
        'ServiceType': customers.ServiceType,
        'Telephone': customers.Telephone,
        'Type': customers.Type,
        # nl.models.routes
        'Route': routes.Route,
        'ChangeNotes': routes.ChangeNote,
        'Sequences': routes.Sequence,
        # sqlalchemy
        'distinct': distinct,
        'func': func,
        'select': select,
        'and_': and_,
        'or_': or_,
        # misc
        'pp': pprint,
    }


