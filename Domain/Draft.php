<?php

namespace Domain;

use Domain\Values\Value;

class Draft
{
    private array $originalValues = [];
    private array $proposals = [];

    public function __construct(
        array $originalValues // indexed by value name
    ) {
        $this->originalValues = $originalValues;
    }

    public function addProposal(
        string $valueName,
        Value $proposedValue,
        string $author,
        ApprovingPolicy $approvingPolicy
    ): ?int {
        $this->proposals[] = new Proposal(
            $valueName,
            $this->originalValues[$valueName],
            $proposedValue,
            $author,
            $approvingPolicy->getApprovers($proposedValue)
        );

        return array_key_last($this->proposals);
    }

    public function accept(int $proposalId, string $approver): void
    {
        $this->proposals[$proposalId]->accept($approver);
    }

    public function reject(int $proposalId, string $approver): void
    {
        $this->proposals[$proposalId]->reject($approver);
    }

    public function canSign()
    {
        /** @var Proposal $proposal */
        foreach($this->proposals as $proposal) {
            if ($proposal->isPending()) {
                return false;
            }
        }

        return true;
    }

    public function sign()
    {
        if ($this->canSign()) {
            return new Contract(
                $this->applyProposals($this->originalValues, $this->proposals)
            );
        }
    }

    /**
     * @param Value[] $values value name => value
     * @param Proposal[] $proposals
     *
     * @return Value[]
     */
    private function applyProposals(array $values, array $proposals): array
    {
        foreach ($proposals as $proposal) {
            if ($proposal->isAccepted()) {
                $values[$proposal->getValueName()] = $proposal->getProposedValue();
            }
        }

        return $values;
    }
}
