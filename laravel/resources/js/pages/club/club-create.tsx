import ClubForm from "@/components/from/club-form";
import club from "@/routes/club";

export default function ClubCreate() {
    return <ClubForm />;
}

ClubCreate.layout = {
    breadcrumbs: [
        {
            title: 'Verein',
            href: club.index(),
        },
        {
            title: 'Verein erstellen',
        },
    ],
};
