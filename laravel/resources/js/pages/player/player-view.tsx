import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Badge} from "@/components/ui/badge";
import {Separator} from "@/components/ui/separator";
import type {Player} from "@/types/types";
import club from "@/routes/club";

export function PlayerView({player, button}: { player: Player, button: any }) {
    return (
        <Card className="max-w-xl border-none shadow-none py-1">
            <CardHeader className="pb-2">
                <CardTitle className="grid grid-cols-2">
                    <p className="text-2xl">{player.firstname} {player.lastname}</p>
                    <div className="flex justify-end">{button}</div>
                </CardTitle>
                <a className="text-muted-foreground text-sm" href={club.show.url(player.club.id)}>{player.club.clubname}</a>
            </CardHeader>

            <Separator/>

            <CardContent className="mt-1 grid grid-cols-2 gap-4">
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Jahrgang</p>
                    <p className="font-medium">{player.year_of_birth}</p>
                </div>
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Verein</p>
                    <a className="font-medium" href={club.show.url(player.club.id)}>{player.club.clubname}</a>
                </div>
                <div className="col-span-2">
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-2">Positionen</p>
                    <div className="flex flex-wrap gap-1.5">
                        {player.positions.map(p => (
                            <Badge key={p.id} variant="secondary">{p.position_code}</Badge>
                        ))}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
