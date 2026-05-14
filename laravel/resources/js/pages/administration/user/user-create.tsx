import {administration} from '@/routes';
import userGroup from "@/routes/administration/user-group";
import UserForm from "@/components/from/user-form";
import user from "@/routes/administration/user";

export default function UserCreate() {
    return <UserForm />;
}

UserCreate.layout = {
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
            title: 'Benutzer erstellen'
        }
    ],
};
