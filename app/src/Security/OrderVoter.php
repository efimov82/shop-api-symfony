<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use App\Entity\CustomerOrder;
use App\Entity\User;

class OrderVoter extends Voter
{
  public function __construct(
    private Security $security,
  ) {
  }

  protected function supports(string $attribute, mixed $subject): bool
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [ACTION_VIEW, ACTION_EDIT, ACTION_DELETE])) {
      return false;
    }

    // only vote on `CustomerOrder` objects
    if (!$subject instanceof CustomerOrder) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return false;
    }

    if ($this->security->isGranted('ROLE_ADMIN')) {
      return true;
    }

    // you know $subject is a CustomerOrder object, thanks to `supports()`
    /** @var CustomerOrder $post */
    $order = $subject;

    return match ($attribute) {
      ACTION_VIEW => $this->canView($order, $user),
      ACTION_EDIT => $this->canEdit($order, $user),
      // TODO - check can resolve here???
      // 'delete' => $this->canDelete($order, $user);
      default => throw new \LogicException('This code should not be reached!')
    };
  }

  private function canView(CustomerOrder $order, User $user): bool
  {
    // if they can edit, they can view
    if ($this->canEdit($order, $user)) {
      return true;
    }

    // the Post object could have, for example, a method `isPrivate()`
    // return !$order->isPrivate();
    return false;// !$order->isPrivate();
  }

  private function canEdit(CustomerOrder $order, User $user): bool
  {
    // TODO: Maybe add object has a `getOwner()` method ??
    return $user === $order->getUser();
  }
}
