import {administration} from '@/routes';
import user from "@/routes/administration/user";
import UserForm from "@/components/from/user-form";

export default function UserEdit() {
    return <UserForm edit backHref={user.index.url()}/>;
}

UserEdit.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Benutzer',
            href: user.index(),
        },
        {
            title: 'Benutzer bearbeiten'
        }
    ],
};
