# coding: utf-8
from flask_sqlalchemy import SQLAlchemy


db = SQLAlchemy()



class Alert(db.Model):
    __tablename__ = 'alerts'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.ForeignKey('users.id', onupdate='CASCADE'), index=True)
    type = db.Column(db.Enum('BILLING', 'PAYMENT', 'CUSTOMER', 'LOGIN'), nullable=False)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    ref = db.Column(db.Integer, nullable=False)
    what = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)
    msg = db.Column(db.Text(collation='utf8_unicode_ci'), nullable=False)

    user = db.relationship('User', primaryjoin='Alert.user_id == User.id', backref='alerts')



class AuditLog(db.Model):
    __tablename__ = 'audit_log'

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    user_id = db.Column(db.ForeignKey('users.id', onupdate='CASCADE'), index=True)
    what = db.Column(db.Text(collation='utf8_unicode_ci'), nullable=False)

    user = db.relationship('User', primaryjoin='AuditLog.user_id == User.id', backref='audit_logs')



class Configuration(db.Model):
    __tablename__ = 'configuration'

    key = db.Column(db.String(255, 'utf8_unicode_ci'), primary_key=True)
    value = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)



class Customer(db.Model):
    __tablename__ = 'customers'

    id = db.Column(db.Integer, primary_key=True)
    route_id = db.Column(db.ForeignKey('routes.id', onupdate='CASCADE'), nullable=False, index=True)
    type_id = db.Column(db.ForeignKey('customers_types.id', onupdate='CASCADE'), nullable=False, index=True)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)
    routeList = db.Column(db.Enum('N', 'Y'), nullable=False)
    started = db.Column(db.Date)
    rateType = db.Column(db.Enum('STANDARD', 'REPLACE', 'SURCHARGE'), nullable=False)
    rateOverride = db.Column(db.Numeric(10, 2), nullable=False)
    billType = db.Column(db.Integer, nullable=False)
    billBalance = db.Column(db.Numeric(10, 2), nullable=False)
    billStopped = db.Column(db.Enum('N', 'Y'), nullable=False)
    billCount = db.Column(db.SmallInteger, nullable=False)
    billPeriod = db.Column(db.ForeignKey('periods.id', ondelete='SET NULL', onupdate='CASCADE'), index=True)
    billQuantity = db.Column(db.SmallInteger, nullable=False)
    billStart = db.Column(db.Date)
    billEnd = db.Column(db.Date)
    billDue = db.Column(db.Date)
    balance = db.Column(db.Numeric(10, 2), nullable=False)
    lastPayment = db.Column(db.ForeignKey('customers_payments.id', ondelete='SET NULL', onupdate='CASCADE'), index=True)
    billNote = db.Column(db.Text(collation='utf8_unicode_ci'))
    notes = db.Column(db.Text(collation='utf8_unicode_ci'))
    deliveryNote = db.Column(db.Text(collation='utf8_unicode_ci'), nullable=False)

    period = db.relationship('Period', primaryjoin='Customer.billPeriod == Period.id', backref='customers')
    customers_payment = db.relationship('CustomersPayment', primaryjoin='Customer.lastPayment == CustomersPayment.id', backref='customers')
    route = db.relationship('Route', primaryjoin='Customer.route_id == Route.id', backref='customers')
    type = db.relationship('CustomersType', primaryjoin='Customer.type_id == CustomersType.id', backref='customers')



class CustomersAddress(db.Model):
    __tablename__ = 'customers_addresses'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False)
    sequence = db.Column(db.SmallInteger, primary_key=True, nullable=False)
    address1 = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    address2 = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    city = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    state = db.Column(db.String(2, 'utf8_unicode_ci'), nullable=False)
    zip = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)

    customer = db.relationship('Customer', primaryjoin='CustomersAddress.customer_id == Customer.id', backref='customers_addresses')



class CustomersAdjustment(db.Model):
    __tablename__ = 'customers_adjustments'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    desc = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)
    amount = db.Column(db.Numeric(6, 2), nullable=False)
    note = db.Column(db.Text(collation='utf8_unicode_ci'))

    customer = db.relationship('Customer', primaryjoin='CustomersAdjustment.customer_id == Customer.id', backref='customers_adjustments')
    period = db.relationship('Period', primaryjoin='CustomersAdjustment.period_id == Period.id', backref='customers_adjustments')



class CustomersBill(db.Model):
    __tablename__ = 'customers_bills'

    cid = db.Column(db.String(6, 'utf8_unicode_ci'), primary_key=True, nullable=False)
    iid = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), primary_key=True, nullable=False, index=True)
    rateType = db.Column(db.Enum('STANDARD', 'REPLACE', 'SURCHARGE'), nullable=False)
    rateOverride = db.Column(db.Numeric(10, 2), nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    when = db.Column(db.DateTime, server_default=db.FetchedValue())
    export = db.Column(db.Enum('N', 'Y'), nullable=False)
    cnm = db.Column(db.String(25, 'utf8_unicode_ci'), nullable=False)
    cad1 = db.Column(db.String(25, 'utf8_unicode_ci'), nullable=False)
    cad2 = db.Column(db.String(25, 'utf8_unicode_ci'), nullable=False)
    ctel = db.Column(db.String(25, 'utf8_unicode_ci'), nullable=False)
    rt = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    dNm = db.Column(db.String(20, 'utf8_unicode_ci'), nullable=False)
    dAd = db.Column(db.String(20, 'utf8_unicode_ci'), nullable=False)
    dCt = db.Column(db.String(11, 'utf8_unicode_ci'), nullable=False)
    dSt = db.Column(db.String(2, 'utf8_unicode_ci'), nullable=False)
    dZp = db.Column(db.String(5, 'utf8_unicode_ci'), nullable=False)
    bNm = db.Column(db.String(22, 'utf8_unicode_ci'), nullable=False)
    bAd1 = db.Column(db.String(22, 'utf8_unicode_ci'), nullable=False)
    bAd2 = db.Column(db.String(22, 'utf8_unicode_ci'), nullable=False)
    bAd3 = db.Column(db.String(22, 'utf8_unicode_ci'), nullable=False)
    bAd4 = db.Column(db.String(22, 'utf8_unicode_ci'), nullable=False)
    rTit = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    rate = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    fwd = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    pmt = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    adj = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    bal = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    due = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    dts = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    dte = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    nt1 = db.Column(db.String(36, 'utf8_unicode_ci'), nullable=False)
    nt2 = db.Column(db.String(36, 'utf8_unicode_ci'), nullable=False)
    nt3 = db.Column(db.String(36, 'utf8_unicode_ci'), nullable=False)
    nt4 = db.Column(db.String(36, 'utf8_unicode_ci'), nullable=False)

    period = db.relationship('Period', primaryjoin='CustomersBill.iid == Period.id', backref='customers_bills')



class CustomersBillsLog(db.Model):
    __tablename__ = 'customers_bills_log'

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    sequence = db.Column(db.SmallInteger, nullable=False)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), nullable=False, index=True)
    what = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)

    customer = db.relationship('Customer', primaryjoin='CustomersBillsLog.customer_id == Customer.id', backref='customers_bills_logs')
    period = db.relationship('Period', primaryjoin='CustomersBillsLog.period_id == Period.id', backref='customers_bills_logs')



class CustomersCombinedBill(db.Model):
    __tablename__ = 'customers_combined_bills'

    customer_id_main = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False)
    customer_id_secondary = db.Column(db.ForeignKey('customers.id', onupdate='CASCADE'), primary_key=True, nullable=False, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())

    customer = db.relationship('Customer', primaryjoin='CustomersCombinedBill.customer_id_main == Customer.id', backref='customer_customers_combined_bills')
    customer1 = db.relationship('Customer', primaryjoin='CustomersCombinedBill.customer_id_secondary == Customer.id', backref='customer_customers_combined_bills_0')



class CustomersComplaint(db.Model):
    __tablename__ = 'customers_complaints'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    type = db.Column(db.Enum('MISSED', 'WET', 'DAMAGED', 'LATE', 'OTHER'), nullable=False)
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)
    result = db.Column(db.Enum('NONE', 'CREDITDAILY', 'CREDITSUNDAY', 'REDELIVERED', 'CREDIT', 'CHARGE'), nullable=False)
    amount = db.Column(db.Numeric(8, 3), nullable=False)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text(collation='utf8_unicode_ci'))

    customer = db.relationship('Customer', primaryjoin='CustomersComplaint.customer_id == Customer.id', backref='customers_complaints')
    period = db.relationship('Period', primaryjoin='CustomersComplaint.period_id == Period.id', backref='customers_complaints')



class CustomersName(db.Model):
    __tablename__ = 'customers_names'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False)
    sequence = db.Column(db.Integer, primary_key=True, nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    title = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    first = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    last = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    surname = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)

    customer = db.relationship('Customer', primaryjoin='CustomersName.customer_id == Customer.id', backref='customers_names')



class CustomersPayment(db.Model):
    __tablename__ = 'customers_payments'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), nullable=False, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    type = db.Column(db.Enum('CHECK', 'MONEYORDER', 'CASH', 'CREDIT'), nullable=False)
    date = db.Column(db.Date, nullable=False)
    amount = db.Column(db.Numeric(10, 2), nullable=False)
    extra1 = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)
    extra2 = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)
    tip = db.Column(db.Numeric(10, 2), nullable=False)
    note = db.Column(db.Text(collation='utf8_unicode_ci'))

    customer = db.relationship('Customer', primaryjoin='CustomersPayment.customer_id == Customer.id', backref='customers_payments')
    period = db.relationship('Period', primaryjoin='CustomersPayment.period_id == Period.id', backref='customers_payments')



class CustomersRate(db.Model):
    __tablename__ = 'customers_rates'

    id = db.Column(db.Integer, primary_key=True)
    type_id = db.Column(db.ForeignKey('customers_types.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id_begin = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), nullable=False, index=True)
    period_id_end = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    rate = db.Column(db.Numeric(6, 2), nullable=False)
    daily_credit = db.Column(db.Numeric(6, 2), nullable=False)
    sunday_credit = db.Column(db.Numeric(6, 2), nullable=False)

    period = db.relationship('Period', primaryjoin='CustomersRate.period_id_begin == Period.id', backref='period_customers_rates')
    period1 = db.relationship('Period', primaryjoin='CustomersRate.period_id_end == Period.id', backref='period_customers_rates_0')
    type = db.relationship('CustomersType', primaryjoin='CustomersRate.type_id == CustomersType.id', backref='customers_rates')



class CustomersService(db.Model):
    __tablename__ = 'customers_service'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    type = db.Column(db.Enum('STOP', 'START'), nullable=False)
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text(collation='utf8_unicode_ci'))

    customer = db.relationship('Customer', primaryjoin='CustomersService.customer_id == Customer.id', backref='customers_services')
    period = db.relationship('Period', primaryjoin='CustomersService.period_id == Period.id', backref='customers_services')



class CustomersServiceType(db.Model):
    __tablename__ = 'customers_service_types'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)
    type_id_from = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), nullable=False, index=True)
    type_id_to = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'), nullable=False, index=True)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text(collation='utf8_unicode_ci'))

    customer = db.relationship('Customer', primaryjoin='CustomersServiceType.customer_id == Customer.id', backref='customers_service_types')
    period = db.relationship('Period', primaryjoin='CustomersServiceType.period_id == Period.id', backref='period_period_customers_service_types')
    period1 = db.relationship('Period', primaryjoin='CustomersServiceType.type_id_from == Period.id', backref='period_period_customers_service_types_0')
    period2 = db.relationship('Period', primaryjoin='CustomersServiceType.type_id_to == Period.id', backref='period_period_customers_service_types')



class CustomersTelephone(db.Model):
    __tablename__ = 'customers_telephones'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False)
    sequence = db.Column(db.SmallInteger, primary_key=True, nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    type = db.Column(db.String(20, 'utf8_unicode_ci'), nullable=False)
    number = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)

    customer = db.relationship('Customer', primaryjoin='CustomersTelephone.customer_id == Customer.id', backref='customers_telephones')



class CustomersType(db.Model):
    __tablename__ = 'customers_types'

    id = db.Column(db.Integer, primary_key=True)
    abbr = db.Column(db.String(10, 'utf8_unicode_ci'), nullable=False)
    name = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    color = db.Column(db.Integer, nullable=False)
    visible = db.Column(db.Enum('N', 'Y'), nullable=False)
    newChange = db.Column(db.Enum('N', 'Y'), nullable=False)
    watchStart = db.Column(db.Enum('N', 'Y'), nullable=False)
    su = db.Column(db.Enum('N', 'Y'), nullable=False)
    mo = db.Column(db.Enum('N', 'Y'), nullable=False)
    tu = db.Column(db.Enum('N', 'Y'), nullable=False)
    we = db.Column(db.Enum('N', 'Y'), nullable=False)
    th = db.Column(db.Enum('N', 'Y'), nullable=False)
    fr = db.Column(db.Enum('N', 'Y'), nullable=False)
    sa = db.Column(db.Enum('N', 'Y'), nullable=False)



class Error(db.Model):
    __tablename__ = 'errors'

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    icode = db.Column(db.Integer, nullable=False)
    ecode = db.Column(db.Integer, nullable=False)
    context = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)
    query = db.Column(db.String(1024, 'utf8_unicode_ci'), nullable=False)
    what = db.Column(db.String(1024, 'utf8_unicode_ci'), nullable=False)



class Group(db.Model):
    __tablename__ = 'groups'

    id = db.Column(db.SmallInteger, primary_key=True)
    name = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)



class GroupsConfiguration(db.Model):
    __tablename__ = 'groups_configuration'

    key = db.Column(db.String(255, 'utf8_unicode_ci'), primary_key=True)
    group_id = db.Column(db.ForeignKey('groups.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    value = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)

    group = db.relationship('Group', primaryjoin='GroupsConfiguration.group_id == Group.id', backref='groups_configurations')



class Period(db.Model):
    __tablename__ = 'periods'

    id = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    changes_start = db.Column(db.Date, nullable=False)
    changes_end = db.Column(db.Date, nullable=False)
    bill = db.Column(db.Date, nullable=False)
    display_start = db.Column(db.Date, nullable=False)
    display_end = db.Column(db.Date, nullable=False)
    due = db.Column(db.Date, nullable=False)
    title = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)



class Route(db.Model):
    __tablename__ = 'routes'

    id = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    title = db.Column(db.String(20, 'utf8_unicode_ci'), nullable=False)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)



class RoutesChangesNote(db.Model):
    __tablename__ = 'routes_changes_notes'

    id = db.Column(db.Integer, primary_key=True)
    date = db.Column(db.Date, nullable=False, index=True)
    route_id = db.Column(db.ForeignKey('routes.id', ondelete='CASCADE', onupdate='CASCADE'), index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    note = db.Column(db.String(collation='utf8_unicode_ci'), nullable=False)

    route = db.relationship('Route', primaryjoin='RoutesChangesNote.route_id == Route.id', backref='routes_changes_notes')



class RoutesSequence(db.Model):
    __tablename__ = 'routes_sequence'

    tag_id = db.Column(db.ForeignKey('customers.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False)
    route_id = db.Column(db.ForeignKey('routes.id', ondelete='CASCADE', onupdate='CASCADE'), primary_key=True, nullable=False, index=True)
    order = db.Column(db.Integer, nullable=False)

    route = db.relationship('Route', primaryjoin='RoutesSequence.route_id == Route.id', backref='routes_sequences')
    tag = db.relationship('Customer', primaryjoin='RoutesSequence.tag_id == Customer.id', backref='customers_sequences')



t_security = db.Table(
    'security',
    db.Column('group_id', db.ForeignKey('groups.id', ondelete='CASCADE', onupdate='CASCADE'), index=True),
    db.Column('user_id', db.ForeignKey('users.id', ondelete='CASCADE', onupdate='CASCADE'), index=True),
    db.Column('page', db.String(20, 'utf8_unicode_ci'), nullable=False),
    db.Column('feature', db.String(20, 'utf8_unicode_ci'), nullable=False),
    db.Column('allowed', db.Enum('N', 'Y'), nullable=False),
    db.Index('group_id', 'group_id', 'user_id', 'page', 'feature')
)



class User(db.Model):
    __tablename__ = 'users'

    id = db.Column(db.SmallInteger, primary_key=True)
    login = db.Column(db.String(20, 'utf8_unicode_ci'), nullable=False)
    password = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)
    name = db.Column(db.String(30, 'utf8_unicode_ci'), nullable=False)
    group_id = db.Column(db.ForeignKey('groups.id', onupdate='CASCADE'), nullable=False, index=True)
    home = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)

    group = db.relationship('Group', primaryjoin='User.group_id == Group.id', backref='users')



class UsersConfiguration(db.Model):
    __tablename__ = 'users_configuration'

    key = db.Column(db.String(255, 'utf8_unicode_ci'), primary_key=True)
    user_id = db.Column(db.ForeignKey('users.id', ondelete='CASCADE', onupdate='CASCADE'), nullable=False, index=True)
    value = db.Column(db.String(255, 'utf8_unicode_ci'), nullable=False)

    user = db.relationship('User', primaryjoin='UsersConfiguration.user_id == User.id', backref='users_configurations')
