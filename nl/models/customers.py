# coding: utf-8

from sqlalchemy import or_

from nl import db


__all__ = [
    'Address',
    'Adjustment',
    'Bill',
    'BillLog',
    'CombinedBill',
    'Complaint',
    'Customer',
    'Name',
    'Payment',
    'Rate',
    'ServiceChange',
    'ServiceType',
    'Telephone',
    'Type',
]

class Customer(db.Model):
    """
    The actual customers.
    """
    __tablename__ = 'customers'

    id = db.Column(db.Integer, primary_key=True)
    route_id = db.Column(db.ForeignKey('routes.id', ondelete='RESTRICT', onupdate='CASCADE'),
                         nullable=False, index=True)
    type_id = db.Column(db.ForeignKey('customers_types.id', ondelete='RESTRICT', onupdate='CASCADE'),
                        nullable=False, index=True)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)
    routeList = db.Column(db.Enum('N', 'Y'), nullable=False)
    started = db.Column(db.Date)
    rateType = db.Column(db.Enum('STANDARD', 'REPLACE', 'SURCHARGE'), nullable=False)
    rateOverride = db.Column(db.Numeric(10, 2), nullable=False)
    billType = db.Column(db.Integer, nullable=False)
    billBalance = db.Column(db.Numeric(10, 2), nullable=False)
    billStopped = db.Column(db.Enum('N', 'Y'), nullable=False)
    billCount = db.Column(db.SmallInteger, nullable=False)
    billPeriod = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                           nullable=True, index=True)
    billQuantity = db.Column(db.SmallInteger, nullable=False)
    billStart = db.Column(db.Date, nullable=True)
    billEnd = db.Column(db.Date, nullable=True)
    billDue = db.Column(db.Date, nullable=True)
    balance = db.Column(db.Numeric(10, 2), nullable=False)
    lastPayment = db.Column(db.ForeignKey('Payment.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=True, index=True)
    billNote = db.Column(db.Text())
    notes = db.Column(db.Text())
    deliveryNote = db.Column(db.Text(), nullable=False)

    period = db.relationship('Period', primaryjoin='Customer.billPeriod == periods.c.id')
    route = db.relationship('Route', primaryjoin='Customer.route_id == Route.id',
                            backref='customer')
    type = db.relationship('Type', primaryjoin='Customer.type_id == customers_types.c.id',
                           backref='customer')

    def address(self, sequence=1):
        """Return specific customer address."""
        return Address.query.filter_by(customer_id=self.id, sequence=sequence).first()

    def combineds(self):
        return CombinedBills.query.filter_by(customer_id_main=self.id).all()

    def rate(self):
        """
        Return the customers current rate.
        """
        # Simply replace standard rate?
        if self.rateType == 'REPLACE':
            return self.rateOverride

        # Locate currently active rate
        period = Configs.get('billing-period')
        r = Rates.query\
            .filter(Rates.type_id==self.type_id)\
            .filter(Rates.period_id_begin<=period)\
            .filter(or_(Rates.period_id_end>=period,
                        Rates.period_id_end==None))\
            .first()

        # Standard rate
        if self.rateType == 'STANDARD':
            return r.rate

        # Surcharge them!
        assert self.rateType == 'SURCHARGE'
        return r.rate + self.rateOverride
        
    def name(self, sequence=1):
        """Return specific customer name."""
        return Names.query.filter_by(customer_id=self.id, sequence=sequence).first()
    
    def telephone(self, sequence=1):
        """Return specific customer telephone."""
        return Telephones.query.filter_by(customer_id=self.id, sequence=sequence).first()


class Address(db.Model):
    """
    Customer addresses.
    """
    __tablename__ = 'customers_addresses'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            primary_key=True)
    sequence = db.Column(db.SmallInteger, primary_key=True)  # TODO:  Convert to Integer
    address1 = db.Column(db.String(30), nullable=False)
    address2 = db.Column(db.String(30), nullable=False)
    city = db.Column(db.String(30), nullable=False)
    state = db.Column(db.String(2), nullable=False)
    zip = db.Column(db.String(10), nullable=False) # TODO:  Rename to postal

    customer = db.relationship('Customer', primaryjoin='Address.customer_id == customers.c.id', backref='addresses')

    # (types) sequence of addresses
    ADD_DELIVERY = 1
    ADD_BILLING = 101


class Adjustment(db.Model):
    """
    Details of amounts to charge or credit to customer.  Summarized on bill.
    """
    __tablename__ = 'customers_adjustments'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    desc = db.Column(db.String(), nullable=False)
    amount = db.Column(db.Numeric(6, 2), nullable=False)
    note = db.Column(db.Text())

    customer = db.relationship('Customer', primaryjoin='Adjustment.customer_id == customers.c.id',
                               backref='adjustments')
    period = db.relationship('Period', primaryjoin='Adjustment.period_id == periods.c.id')


class Bill(db.Model):
    """
    Customer bills, for mail merge.

    Fields with limited width are CUT OFF.  No abbreviation attempted.
    """
    __tablename__ = 'customers_bills'

    cid = db.Column(db.String(6), primary_key=True)
    iid = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                    primary_key=True)
    rateType = db.Column(db.Enum('STANDARD', 'REPLACE', 'SURCHARGE'), nullable=False)
    rateOverride = db.Column(db.Numeric(10, 2), nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    when = db.Column(db.DateTime)
    export = db.Column(db.Enum('N', 'Y'), nullable=False)
    cnm = db.Column(db.String(25), nullable=False)
    cad1 = db.Column(db.String(25), nullable=False)
    cad2 = db.Column(db.String(25), nullable=False)
    ctel = db.Column(db.String(25), nullable=False)
    rt = db.Column(db.String(10), nullable=False)
    dNm = db.Column(db.String(20), nullable=False)
    dAd = db.Column(db.String(20), nullable=False)
    dCt = db.Column(db.String(11), nullable=False)
    dSt = db.Column(db.String(2), nullable=False)
    dZp = db.Column(db.String(5), nullable=False)
    bNm = db.Column(db.String(22), nullable=False)
    bAd1 = db.Column(db.String(22), nullable=False)
    bAd2 = db.Column(db.String(22), nullable=False)
    bAd3 = db.Column(db.String(22), nullable=False)
    bAd4 = db.Column(db.String(22), nullable=False)
    rTit = db.Column(db.String(30), nullable=False)
    rate = db.Column(db.String(10), nullable=False)
    fwd = db.Column(db.String(10), nullable=False)
    pmt = db.Column(db.String(10), nullable=False)
    adj = db.Column(db.String(10), nullable=False)
    bal = db.Column(db.String(10), nullable=False)
    due = db.Column(db.String(10), nullable=False)
    dts = db.Column(db.String(10), nullable=False)
    dte = db.Column(db.String(10), nullable=False)
    nt1 = db.Column(db.String(36), nullable=False)
    nt2 = db.Column(db.String(36), nullable=False)
    nt3 = db.Column(db.String(36), nullable=False)
    nt4 = db.Column(db.String(36), nullable=False)

    period = db.relationship('Period', primaryjoin='Bill.iid == Period.id')


class BillLog(db.Model):
    """
    Detailed process when generating bill.  Usefull to see what a bill was unable to
    be generated without an error.
    """
    __tablename__ = 'customers_bills_log'

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    sequence = db.Column(db.SmallInteger, nullable=False)    # TODO: Convert to integer
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          nullable=False, index=True)
    what = db.Column(db.String(255), nullable=False)

    customer = db.relationship('Customer', primaryjoin='BillLog.customer_id == Customer.id',
                               backref='bill_logs')
    period = db.relationship('Period', primaryjoin='BillLog.period_id == Period.id')


class CombinedBill(db.Model):
    """
    When billing, add secondary bill amount/adjustments/payments to main bill.
    Don't bill secondary directly.
    """
    __tablename__ = 'customers_combined_bills'

    customer_id_main = db.Column(db.ForeignKey('Customer.id', ondelete='RESTRICT', onupdate='CASCADE'),
                                 primary_key=True)
    customer_id_secondary = db.Column(db.ForeignKey('Customer.id', ondelete='RESTRICT', onupdate='CASCADE'),
                                      primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)

    
class Complaint(db.Model):
    """
    Customer delivery problems.
    """
    __tablename__ = 'customers_complaints'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    type = db.Column(db.Enum('MISSED', 'WET', 'DAMAGED', 'LATE', 'OTHER'), nullable=False)
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(255), nullable=False)
    result = db.Column(db.Enum('NONE', 'CREDITDAILY', 'CREDITSUNDAY',
                               'REDELIVERED', 'CREDIT', 'CHARGE'),
                       nullable=False)
    amount = db.Column(db.Numeric(8, 3), nullable=False)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text())

    customer = db.relationship('Customer', primaryjoin='Complaint.customer_id == Customer.id',
                               backref='complaints')


class Name(db.Model):
    """
    Customer names.
    """
    __tablename__ = 'customers_names'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            primary_key=True)
    sequence = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    title = db.Column(db.String(10), nullable=False)
    first = db.Column(db.String(30), nullable=False)
    last = db.Column(db.String(30), nullable=False)
    surname = db.Column(db.String(10), nullable=False)

    customer = db.relationship('Customer', primaryjoin='Name.customer_id == Customer.id',
                               backref='names')

    # sequence types
    NAM_DELIVERY1 = 1
    NAM_DELIVERY2 = 2
    NAM_BILLING1 = 101
    NAM_BILLING2 = 102
    

class Payment(db.Model):
    """
    Payments from customers.
    """
    __tablename__ = 'customers_payments'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          nullable=False, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False, server_default=db.FetchedValue())
    type = db.Column(db.Enum('CHECK', 'MONEYORDER', 'CASH', 'CREDIT'), nullable=False)
    date = db.Column(db.Date, nullable=False)
    amount = db.Column(db.Numeric(10, 2), nullable=False)
    extra1 = db.Column(db.String(), nullable=False)
    extra2 = db.Column(db.String(), nullable=False)
    tip = db.Column(db.Numeric(10, 2), nullable=False)
    note = db.Column(db.Text())

    customer = db.relationship('Customer', primaryjoin='Payment.customer_id == Customer.id',
                               backref='payments')

    
class Rate(db.Model):
    """
    Rates to charge for specific delivery type.
    """
    __tablename__ = 'customers_rates'

    id = db.Column(db.Integer, primary_key=True)
    type_id = db.Column(db.ForeignKey('customers_types.id', ondelete='RESTRICT', onupdate='CASCADE'),
                        nullable=False, index=True)
    period_id_begin = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'),
                                nullable=False, index=True)
    period_id_end = db.Column(db.ForeignKey('periods.id', onupdate='CASCADE'),
                              nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    rate = db.Column(db.Numeric(6, 2), nullable=False)
    daily_credit = db.Column(db.Numeric(6, 2), nullable=False)
    sunday_credit = db.Column(db.Numeric(6, 2), nullable=False)

    type = db.relationship('Type', primaryjoin='Rate.type_id == Type.id', backref='rates')


class ServiceChange(db.Model):
    """
    Customer delivery status changes.
    """
    __tablename__ = 'customers_service'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    type = db.Column(db.Enum('STOP', 'START'), nullable=False)
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(255), nullable=False)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text())

    customer = db.relationship('Customer', primaryjoin='ServiceChange.customer_id == Customer.id',
                               backref='service_changes')


class ServiceType(db.Model):
    """
    Customer changing from one delivery type to another.
    """
    __tablename__ = 'customers_service_types'

    id = db.Column(db.Integer, primary_key=True)
    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, index=True)
    period_id = db.Column(db.ForeignKey('periods.id', ondelete='RESTRICT', onupdate='CASCADE'),
                          nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    when = db.Column(db.Date, nullable=False)
    why = db.Column(db.String(), nullable=False)
    type_id_from = db.Column(db.ForeignKey('customers_types.id', ondelete='RESTRICT', onupdate='CASCADE'),
                             nullable=False, index=True)
    type_id_to = db.Column(db.ForeignKey('customers_types.id', ondelete='RESTRICT', onupdate='CASCADE'),
                           nullable=False, index=True)
    ignoreOnBill = db.Column(db.Enum('N', 'Y'), nullable=False)
    note = db.Column(db.Text())

    customer = db.relationship('Customer', primaryjoin='ServiceType.customer_id == Customer.id',
                               backref='service_types')


class Telephone(db.Model):
    """
    Customers telephones.
    """
    __tablename__ = 'customers_telephones'

    customer_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                            nullable=False, primary_key=True)
    sequence = db.Column(db.SmallInteger, nullable=False, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    type = db.Column(db.String(20), nullable=False)
    number = db.Column(db.String(30), nullable=False)

    customer = db.relationship('Customer', primaryjoin='Telephone.customer_id == Customer.id',
                               backref='telephones')

    # sequence values
    TEL_DELIVERY1 = 1
    TEL_DELIVERY2 = 2
    TEL_DELIVERY3 = 3
    TEL_BILLING1 = 101
    TEL_BILLING2 = 102
    TEL_BILLING3 = 103

    
class Type(db.Model):
    """
    Customer delivery type.
    """
    __tablename__ = 'customers_types'

    id = db.Column(db.Integer, primary_key=True)
    abbr = db.Column(db.String(10), nullable=False)
    name = db.Column(db.String(30), nullable=False)
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


