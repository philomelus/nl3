# coding: utf-8

from werkzeug.security import generate_password_hash, check_password_hash
from flask_login import current_user, UserMixin

from nl import db

@login.user_loader
def load_user(id):
    return User.query.get(int(id))


class Group(db.Model):
    """
    Groups to allow common security and profile settings for classes of users.

    LIMITATION:  Currently only a SINGLE group allowed per user ...
                 which reduces the usefulness of this considerably...
    """
    __tablename__ = 'groups'

    id = db.Column(db.SmallInteger, primary_key=True)
    name = db.Column(db.String(), nullable=False)

    def __repr__(self):
        return '<Group {}:{}>'.format(self.name, self.id)

    
class Security(db.Model):
    """
    For a specific page and feature, whether a particular user/group/anybody is allowed
    to acually access/use the page/feature.

    If group_id and user_id are Null, then effect is global.
    If group_id is Null and user_id specified, effect is user specific.
    If group_id is specified and user_id is Null, effect is group specific.

    If group_id and user_id are specified, security failures will occur.
    """
    __tablename__ = 'security'

    group_id = db.Column(db.ForeignKey('Group.id', ondelete='CASCADE', onupdate='CASCADE'),
                         nullable=True, primary_key=True)
    user_id = db.Column(db.ForeignKey('User.id', ondelete='CASCADE', onupdate='CASCADE'),
                        nullable=True, primary_key=True)
    page = db.Column(db.String(20), nullable=False, primary_key=True)
    feature = db.Column(db.String(20), nullable=False, primary_key=True)
    allowed = db.Column(db.Enum('N', 'Y'), nullable=False)  
    

class User(UserMixin, db.Model):
    """
    Users of newsledger.  NOT customers (well, they could be both ...).
    """
    __tablename__ = 'users'

    id = db.Column(db.SmallInteger, primary_key=True)
    login = db.Column(db.String(20), nullable=False)
    password = db.Column(db.String(255), nullable=False)
    name = db.Column(db.String(30), nullable=False)
    group_id = db.Column(db.ForeignKey('groups.id', ondelete='CASCADE', onupdate='CASCADE'),
                         nullable=False, index=True)
    home = db.Column(db.String(255), nullable=False)

    group = db.relationship('Group', primaryjoin='User.group_id == Group.id', backref='users')

    def __repr__(self):
        return '<User {}:{}>'.format(self.login, self.id)

    def set_password(self, password):
        self.password = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.password, password)

