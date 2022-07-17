from pprint import pprint

from sqlalchemy import distinct, func, select, and_, or_

import nl
from nl import app, db
from nl import models
from nl.models import auth, config, customers, routes


@app.shell_context_processor
def make_shell_context():
    return {
        "app": app,
        "db": db,
        "security": app.security,
        "user_store": app.user_datastore,
        # nl.models
        "Error": models.Error,
        "Period": models.Period,
        # nl.models.auth
        "Role": auth.Role,
        "RolesUsers": auth.RolesUsers,
        "User": auth.User,
        # nl.models.config
        "Config": config.Config,
        "UserConfig": config.UserConfig,
        # nl.models.customers
        "Customer": customers.Customer,
        "Address": customers.Address,
        "Adjustment": customers.Adjustment,
        "Bill": customers.Bill,
        "BillLog": customers.BillLog,
        "CombinedBill": customers.CombinedBill,
        "Complaint": customers.Complaint,
        "Name": customers.Name,
        "Payment": customers.Payment,
        "Rate": customers.Rate,
        "ServiceChange": customers.ServiceChange,
        "ServiceType": customers.ServiceType,
        "Telephone": customers.Telephone,
        "Type": customers.Type,
        # nl.models.routes
        "Route": routes.Route,
        "ChangeNote": routes.ChangeNote,
        "Sequence": routes.Sequence,
        # sqlalchemy
        "distinct": distinct,
        "func": func,
        "select": select,
        "and_": and_,
        "or_": or_,
        # misc
        "pp": pprint,
    }
