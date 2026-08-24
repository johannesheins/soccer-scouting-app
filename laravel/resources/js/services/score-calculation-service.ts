import {EvaluationCriteriaGroups, EvaluationCriteriaScore} from "@/types/types";

type scores = Record<number, number>;
type CriteriaScore = number[];
export class ScoreCalculationService {
    scores: scores;
    criteriaGroups: EvaluationCriteriaGroups[];
    groupScores: CriteriaScore;
    totalScore: number;

    constructor(scores: Record<number, number>|number[]|EvaluationCriteriaScore[], criteriaGroups: EvaluationCriteriaGroups[]) {
        if(Array.isArray(scores) && scores.every(score => typeof score === 'object')) {
            scores = Object.fromEntries(
                (scores as EvaluationCriteriaScore[]).map(score => [score.evaluation_criteria_id, score.score])
            ) as Record<number, number>;
        }

        this.scores = scores as Record<number, number>;

        this.criteriaGroups = criteriaGroups;
        this.groupScores = [];
        this.totalScore = 0;

        this.calculate()
    }

    public calculate() {
        this.criteriaGroups.forEach(group => {
            this.groupScores[group.id] = 0;
            group.evaluation_criteria.forEach(evaluationCriteria => {
                const score = this.scores[evaluationCriteria.id] ?? 0
                this.groupScores[group.id] += score;
                this.totalScore += score;
            })
        })
    }

    public getTotalScore(){
        return this.totalScore;
    }

    public getGroupScores(){
        return this.groupScores;
    }

    public getGroupScore(groupId: number){
        return this.groupScores[groupId];
    }
}
