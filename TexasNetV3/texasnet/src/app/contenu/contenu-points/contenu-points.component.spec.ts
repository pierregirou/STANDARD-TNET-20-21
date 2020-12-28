import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuPointsComponent } from './contenu-points.component';

describe('ContenuPointsComponent', () => {
  let component: ContenuPointsComponent;
  let fixture: ComponentFixture<ContenuPointsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuPointsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuPointsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
