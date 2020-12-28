import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { HistoryRepresentantComponent } from './history-representant.component';

describe('HistoryRepresentantComponent', () => {
  let component: HistoryRepresentantComponent;
  let fixture: ComponentFixture<HistoryRepresentantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HistoryRepresentantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HistoryRepresentantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
