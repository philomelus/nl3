# coding: utf-8

from nl import db


__all__ = [
    'Route',
    'ChangeNotes',
    'Sequences',
]

class Route(db.Model):
    """
    Details of a specific route.  Each customer can only be on a single route.
    """
    __tablename__ = 'routes'

    id = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    title = db.Column(db.String(20), nullable=False)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)


class ChangeNotes(db.Model):
    """
    For daily change list (for drivers), a global note (when route_id = NULL),
    as well as a route specific note.  Allows communication to drivers w/o
    talking face to face (or used as a reminder).
    """
    __tablename__ = 'routes_changes_notes'

    id = db.Column(db.Integer, primary_key=True)
    date = db.Column(db.Date, nullable=False, index=True)
    route_id = db.Column(db.ForeignKey('Route.id', ondelete='RESTRICT', onupdate='CASCADE'),
                         nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    note = db.Column(db.String(), nullable=False)

    route = db.relationship('Route', primaryjoin='ChangeNotes.route_id == Route.id',
                            backref='change_notes')


class Sequences(db.Model):
    """
    For each customer on each route, the order of the customer in relation to other customers
    on the same route.
    """
    __tablename__ = 'routes_sequence'

    tag_id = db.Column(db.ForeignKey('Customer.id', ondelete='RESTRICT', onupdate='CASCADE'),
                       primary_key=True)
    route_id = db.Column(db.ForeignKey('Route.id', ondelete='CASCADE', onupdate='CASCADE'),
                         primary_key=True)
    order = db.Column(db.Integer, nullable=False)

    route = db.relationship('Route', primaryjoin='Sequences.route_id == Route.id',
                            backref='sequences')
    customer = db.relationship('Customer', primaryjoin='Sequences.tag_id == Customer.id',
                               backref='sequence')
    
