import {administration} from '@/routes';
import userGroup from "@/routes/administration/user-group";
import UserGroupForm from "@/pages/administration/user-group/user-group-form";

export default function UserGroupEdit() {
    return <UserGroupForm edit backHref={userGroup.index.url()}/>;
}

UserGroupEdit.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Benutzergruppen',
            href: userGroup.index(),
        },
        {
            title: 'Benutzergruppe bearbeiten'
        }
    ],
};
