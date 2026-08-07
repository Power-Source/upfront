(function($){
    var l10n = Upfront.Settings && Upfront.Settings.l10n
            ? Upfront.Settings.l10n.global.views
            : Upfront.mainData.l10n.global.views
        ;
    define([

    ], function () {
        return Backbone.View.extend({
            className: "sidebar-profile",
            render: function () {
                var user = Upfront.data.currentUser;
                if ( !user ) user = new Backbone.Model();
                var data = user.get('data') || {},
                    roles = user.get('roles') || [],
                        avatar_url = Upfront.Util.get_avatar(user, 25),
                    tpl
                    ;
                    tpl = '<div class="sidebar-profile-avatar"><img src="{{ avatar_url }}" class="avatar" width="25" height="25" style="width:25px;height:25px;object-fit:cover;" /></div>' +
                    '<div class="sidebar-profile-detail"><span class="sidebar-profile-name">{{name}}</span><span class="sidebar-profile-role">{{role}}</span></div>';
                this.$el.html(_.template(tpl,
                    {
                            avatar_url: avatar_url,
                        name: data.display_name || l10n.anonymous,
                        role: roles[0] || l10n.none,
                        edit_url: Upfront.Settings.admin_url + 'profile.php'
                    }
                ));
            }
        });

    });
}(jQuery));
