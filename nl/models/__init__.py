# coding: utf-8

from nl import db


__all__ = [
    "AuditLog",
    "Error",
    "Period",
]


class Alert(db.Model):
    """
    Provides a way to allow messages to be sent under certian conditions.  Its not
    uncommon to need to perform a specific action, but only when billing, or on
    payment submission, or on a speific date or when a specific customer is modified.
    This table tracks the data for those messages, and the software uses it to
    pass the messages on to the end user at the desired date/time/etc.
    """

    __tablename__ = "alerts"

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(
        db.ForeignKey("auth_users.id", ondelete="CASCADE", onupdate="CASCADE")
    )
    type = db.Column(db.Enum("BILLING", "PAYMENT", "CUSTOMER", "LOGIN"), nullable=False)
    active = db.Column(db.Enum("N", "Y"), nullable=False)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    ref = db.Column(db.Integer, nullable=False)
    what = db.Column(db.String(), nullable=False)
    msg = db.Column(db.Text(), nullable=False)

    user = db.relationship(
        "User", primaryjoin="Alert.user_id == auth_users.c.id", backref="alerts"
    )


class AuditLog(db.Model):
    """
    Any time any changes are made to a customer, route, etc., the date, time,
    and the detail of that change is written here.

    While this can be used to figure out who to blame, the real intention is
    to have a way to determine what actually caused a particular situation,
    as what end users describe and and what reality is, is sometime not in
    agreement (from terminology and niavety rather than malice in 99% of
    cases).
    """

    __tablename__ = "audit_log"

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    user_id = db.Column(db.ForeignKey("auth_users.id"))
    what = db.Column(db.Text(), nullable=False)

    user = db.relationship(
        "User", primaryjoin="AuditLog.user_id == User.id", backref="audit_logs"
    )


class Error(db.Model):
    """
    Place to track errors that occur during execution.  Usefull for debugging
    purposes only (but invaluable in that case, as there there isn't a way to
    pass this info to the end user for reporting back the specific reasons
    for a failure).

    icode is internal error code
    ecode is error coded returned from callables (e.g. OS)
    """

    __tablename__ = "errors"

    id = db.Column(db.Integer, primary_key=True)
    when = db.Column(db.DateTime, nullable=False)
    icode = db.Column(db.Integer, nullable=False)
    ecode = db.Column(db.Integer, nullable=False)
    context = db.Column(db.String(255), nullable=False)
    query = db.Column(db.String(1024), nullable=False)
    what = db.Column(db.String(1024), nullable=False)


class Period(db.Model):
    """
    Date ranges that associate customer rates and customer types.
    """

    __tablename__ = "periods"

    id = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    changes_start = db.Column(db.Date, nullable=False)
    changes_end = db.Column(db.Date, nullable=False)
    bill = db.Column(db.Date, nullable=False)
    display_start = db.Column(db.Date, nullable=False)
    display_end = db.Column(db.Date, nullable=False)
    due = db.Column(db.Date, nullable=False)
    title = db.Column(db.String(30), nullable=False)
