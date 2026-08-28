import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Separator} from "@/components/ui/separator";
import type {Club} from "@/types/types";

export function ClubView({club, button}: { club: Club, button?: any }) {
    return (
        <Card className="border-none shadow-none py-1 w-full">
            <CardHeader className="pb-2">
                <CardTitle className={button && 'grid grid-cols-2'}>
                    <p className="text-2xl">{club.clubname}</p>
                    {button && <div className="flex justify-end">{button}</div>}
                </CardTitle>
            </CardHeader>

            <Separator/>

            <CardContent className="mt-1 grid sm:grid-cols-2 gap-4">
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">PLZ</p>
                    <p className="font-medium">{club.zip_code}</p>
                </div>
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Stadt</p>
                    <p className="font-medium">{club.city}</p>
                </div>
            </CardContent>
        </Card>
    );
}
